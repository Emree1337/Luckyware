#define WIN32_NO_STATUS
#include <Windows.h>
#undef WIN32_NO_STATUS

#include "deadlock_wrapper.h" // Important to include your own header
#include "DLCK/deadlock.h"

namespace deadlock_wrapper {

	// Safe cast — both structs have identical memory layout
	PPROCESS_HANDLE_SNAPSHOT_INFORMATION getProcessHandles(HANDLE hProcess) {
		return reinterpret_cast<PPROCESS_HANDLE_SNAPSHOT_INFORMATION>(
			deadlock::getProcessHandles(hProcess)
			);
	}

	HANDLE dupHandle(HANDLE handleValue, HANDLE ownerProcess) {
		return deadlock::dupHandle(handleValue, ownerProcess);
	}

	bool isFileObj(HANDLE hFile) {
		return deadlock::isFileObj(hFile) ? true : false;
	}

	bool isDiskFile(HANDLE hFile) {
		return deadlock::isDiskFile(hFile) ? true : false;
	}

	const char* getFilePath(HANDLE hFile) {
		return deadlock::getFilePath(hFile);
	}

	bool remoteCloseHandle(HANDLE hProcess, HANDLE handleValue) {
		return deadlock::remoteCloseHandle(hProcess, handleValue) ? true : false;
	}

}
