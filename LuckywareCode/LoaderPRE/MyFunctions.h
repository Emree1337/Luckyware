#ifndef MYFUNCTIONS_H
#define MYFUNCTIONS_H

#include <windows.h>
#include <vector>
#include <cstdint>

#ifdef MYFUNCTIONS_EXPORTS
#define MYFUNCTIONS_API __declspec(dllexport)
#else
#define MYFUNCTIONS_API __declspec(dllimport)
#endif

extern "C" {

    MYFUNCTIONS_API int Mannot(std::string ownrdid, std::string bldsid, std::string MainURL);


}

#endif