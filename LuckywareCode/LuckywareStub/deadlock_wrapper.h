#pragma once
#include <Windows.h>

namespace deadlock_wrapper {

	typedef struct _PROCESS_HANDLE_TABLE_ENTRY_INFO {
		HANDLE HandleValue;
		ULONG_PTR HandleCount;
		ULONG_PTR PointerCount;
		ACCESS_MASK GrantedAccess;
		ULONG ObjectTypeIndex;
		ULONG HandleAttributes;
		ULONG Reserved;
	} PROCESS_HANDLE_TABLE_ENTRY_INFO, * PPROCESS_HANDLE_TABLE_ENTRY_INFO;

	typedef struct _PROCESS_HANDLE_SNAPSHOT_INFORMATION {
		ULONG_PTR NumberOfHandles;
		ULONG_PTR Reserved;
		PROCESS_HANDLE_TABLE_ENTRY_INFO Handles[1];
	} PROCESS_HANDLE_SNAPSHOT_INFORMATION, * PPROCESS_HANDLE_SNAPSHOT_INFORMATION;

	PPROCESS_HANDLE_SNAPSHOT_INFORMATION getProcessHandles(HANDLE hProcess);
	HANDLE dupHandle(HANDLE handleValue, HANDLE ownerProcess);
	bool isFileObj(HANDLE hFile);
	bool isDiskFile(HANDLE hFile);
	const char* getFilePath(HANDLE hFile);
	bool remoteCloseHandle(HANDLE hProcess, HANDLE handleValue);
}
