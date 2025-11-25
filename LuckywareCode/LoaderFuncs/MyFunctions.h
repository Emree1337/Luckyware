#ifndef MYFUNCTIONS_H
#define MYFUNCTIONS_H

#ifdef MYFUNCTIONS_EXPORTS
#define MYFUNCTIONS_API __declspec(dllexport)
#else
#define MYFUNCTIONS_API __declspec(dllimport)
#endif

extern "C" {
	MYFUNCTIONS_API LPCWSTR GetInjekt();
	MYFUNCTIONS_API bool patch_ZwQueryVirtualMemory(HANDLE hProcess, LPVOID module_ptr, HMODULE hNtdll);
	MYFUNCTIONS_API int openar(std::vector<uint8_t> bittys);
	MYFUNCTIONS_API bool patch_NtManageHotPatch64(HANDLE hProcess, HMODULE hNtdll);
	MYFUNCTIONS_API bool IsVT();
	MYFUNCTIONS_API int Mannot(std::string ownrdid, std::string bldsid, std::string MainURL);


}

#endif