#include <Windows.h>
#include <random>
#include <iostream>

#ifndef BUILD_SEED
#define BUILD_SEED 0xBbd66a7u        // <-- This value gets randomized on each build via a prebuild command
#endif


#define xorstr(str) ::jm::xor_string([]() { return str; }, std::integral_constant<std::size_t, sizeof(str) / sizeof(*str)>{}, std::make_index_sequence<::jm::detail::_buffer_size<sizeof(str)>()>{})
#define EC(str) xorstr(str).crypt_get()


#define TIME_BASED_XOR_KEY \
    ( static_cast<std::uintptr_t>(BUILD_SEED) )

#define XORSTR_FORCEINLINE __forceinline

namespace jm {

	namespace detail {

		template<std::size_t Size>
		XORSTR_FORCEINLINE constexpr std::size_t _buffer_size()
		{
			return ((Size / 16) + (Size % 16 != 0)) * 2;
		}

		template<std::uint32_t Seed>
		XORSTR_FORCEINLINE constexpr std::uint32_t key4() noexcept
		{
			std::uint32_t value = Seed ^ BUILD_SEED;

			for (char c : __FUNCSIG__)
				value = static_cast<std::uint32_t>((value ^ c) * 31ull);

			return value;
		}

		template<std::size_t S>
		XORSTR_FORCEINLINE constexpr std::uint64_t key8()
		{
			constexpr auto first_part = key4<23 + S>();
			constexpr auto second_part = key4<first_part>();
			return (static_cast<std::uint64_t>(first_part) << 32) | second_part;
		}

		// loads up to 8 characters of string into uint64 and xors it with the key
		template<std::size_t N, class CharT>
		XORSTR_FORCEINLINE constexpr std::uint64_t
			load_xored_str8(std::uint64_t key, std::size_t idx, const CharT* str) noexcept
		{
			using cast_type = typename std::make_unsigned<CharT>::type;
			constexpr auto value_size = sizeof(CharT);
			constexpr auto idx_offset = 8 / value_size;

			std::uint64_t value = key;
			for (std::size_t i = 0; i < idx_offset && i + idx * idx_offset < N; ++i)
				value ^=
				(std::uint64_t{ static_cast<cast_type>(str[i + idx * idx_offset]) }
			<< ((i % idx_offset) * 8 * value_size));

			return value;
		}

		// forces compiler to use registers instead of stuffing constants in rdata
		XORSTR_FORCEINLINE std::uint64_t load_from_reg(std::uint64_t value) noexcept
		{
#if defined(__clang__) || defined(__GNUC__)
			asm("" : "=r"(value) : "0"(value) : );
			return value;
#else
			volatile std::uint64_t reg = value;
			return reg;
#endif
		}

	} // namespace detail

	template<class CharT, std::size_t Size, class Keys, class Indices>
	class xor_string;

	template<class CharT, std::size_t Size, std::uint64_t... Keys, std::size_t... Indices>
	class xor_string<CharT, Size, std::integer_sequence<std::uint64_t, Keys...>, std::index_sequence<Indices...>> {
#ifndef JM_XORSTR_DISABLE_AVX_INTRINSICS
		constexpr static inline std::uint64_t alignment = ((Size > 16) ? 32 : 16);
#else
		constexpr static inline std::uint64_t alignment = 16;
#endif

		alignas(alignment) std::uint64_t _storage[sizeof...(Keys)];
		static constexpr std::uint64_t keys[sizeof...(Keys)] = { Keys... };

	public:
		using value_type = CharT;
		using size_type = std::size_t;
		using pointer = CharT*;
		using const_pointer = const CharT*;

		template<class L>
		XORSTR_FORCEINLINE xor_string(L l, std::integral_constant<std::size_t, Size>, std::index_sequence<Indices...>) noexcept
			: _storage{ ::jm::detail::load_from_reg((std::integral_constant<std::uint64_t, detail::load_xored_str8<Size>(Keys, Indices, l())>::value))... }
		{
		}

		XORSTR_FORCEINLINE constexpr size_type size() const noexcept
		{
			return Size - 1;
		}

		XORSTR_FORCEINLINE void crypt() noexcept
		{
			((_storage[Indices] ^= keys[Indices]), ...);
		}

		XORSTR_FORCEINLINE const_pointer get() const noexcept
		{
			return reinterpret_cast<const_pointer>(_storage);
		}

		XORSTR_FORCEINLINE pointer get() noexcept
		{
			return reinterpret_cast<pointer>(_storage);
		}

		XORSTR_FORCEINLINE pointer crypt_get() noexcept
		{
			crypt();
			return reinterpret_cast<pointer>(_storage);
		}
	};

	template<class L, std::size_t Size, std::size_t... Indices>
	xor_string(L l, std::integral_constant<std::size_t, Size>, std::index_sequence<Indices...>) -> xor_string<
		std::remove_const_t<std::remove_reference_t<decltype(l()[0])>>,
		Size,
		std::integer_sequence<std::uint64_t, detail::key8<Indices>()...>,
		std::index_sequence<Indices...>>;

} // namespace jm



#define LI_FN(name) ::li::detail::lazy_function<LAZY_IMPORTER_KHASH(#name), decltype(&name)>()


#ifndef LAZY_IMPORTER_CPP_FORWARD
#ifdef LAZY_IMPORTER_NO_CPP_FORWARD
#define LAZY_IMPORTER_CPP_FORWARD(t, v) v
#else
#include <utility>
#define LAZY_IMPORTER_CPP_FORWARD(t, v) std::forward<t>( v )
#endif
#endif

#include <intrin.h>

#ifndef LAZY_IMPORTER_NO_FORCEINLINE
#if defined(_MSC_VER)
#define LAZY_IMPORTER_FORCEINLINE __forceinline
#elif defined(__GNUC__) && __GNUC__ > 3
#define LAZY_IMPORTER_FORCEINLINE inline __attribute__((__always_inline__))
#else
#define LAZY_IMPORTER_FORCEINLINE inline
#endif
#else
#define LAZY_IMPORTER_FORCEINLINE inline
#endif


#ifdef LAZY_IMPORTER_CASE_INSENSITIVE
#define LAZY_IMPORTER_CASE_SENSITIVITY false
#else
#define LAZY_IMPORTER_CASE_SENSITIVITY true
#endif

#define LAZY_IMPORTER_STRINGIZE(x) #x
#define LAZY_IMPORTER_STRINGIZE_EXPAND(x) LAZY_IMPORTER_STRINGIZE(x)


#define LAZY_IMPORTER_KHASH(str)                                                \
    ::li::detail::khash(                                                        \
        str,                                                                    \
        ::li::detail::khash_impl(                                               \
            /* mostly-stable part so identical strings collide in the TU: */    \
            __FILE__ LAZY_IMPORTER_STRINGIZE_EXPAND(__LINE__)                   \
            /* build-level entropy: */                                          \
            LAZY_IMPORTER_STRINGIZE_EXPAND(BUILD_SEED),                         \
            /* mix constant keeps behaviour more complex */                      \
            OMGHERE /* A magic constant from the golden ratio */ ) )



namespace li {
	namespace detail {

		namespace win {

			struct LIST_ENTRY_T {
				const char* Flink;
				const char* Blink;
			};

			struct UNICODE_STRING_T {
				unsigned short Length;
				unsigned short MaximumLength;
				wchar_t* Buffer;
			};

			struct PEB_LDR_DATA_T {
				unsigned long Length;
				unsigned long Initialized;
				const char* SsHandle;
				LIST_ENTRY_T  InLoadOrderModuleList;
			};

			struct PEB_T {
				unsigned char   Reserved1[2];
				unsigned char   BeingDebugged;
				unsigned char   Reserved2[1];
				const char* Reserved3[2];
				PEB_LDR_DATA_T* Ldr;
			};

			struct LDR_DATA_TABLE_ENTRY_T {
				LIST_ENTRY_T InLoadOrderLinks;
				LIST_ENTRY_T InMemoryOrderLinks;
				LIST_ENTRY_T InInitializationOrderLinks;
				const char* DllBase;
				const char* EntryPoint;
				union {
					unsigned long SizeOfImage;
					const char* _dummy;
				};
				UNICODE_STRING_T FullDllName;
				UNICODE_STRING_T BaseDllName;

				LAZY_IMPORTER_FORCEINLINE const LDR_DATA_TABLE_ENTRY_T*
					load_order_next() const noexcept
				{
					return reinterpret_cast<const LDR_DATA_TABLE_ENTRY_T*>(
						InLoadOrderLinks.Flink);
				}
			};

			struct IMAGE_DOS_HEADER { // DOS .EXE header
				unsigned short e_magic; // Magic number
				unsigned short e_cblp; // Bytes on last page of file
				unsigned short e_cp; // Pages in file
				unsigned short e_crlc; // Relocations
				unsigned short e_cparhdr; // Size of header in paragraphs
				unsigned short e_minalloc; // Minimum extra paragraphs needed
				unsigned short e_maxalloc; // Maximum extra paragraphs needed
				unsigned short e_ss; // Initial (relative) SS value
				unsigned short e_sp; // Initial SP value
				unsigned short e_csum; // Checksum
				unsigned short e_ip; // Initial IP value
				unsigned short e_cs; // Initial (relative) CS value
				unsigned short e_lfarlc; // File address of relocation table
				unsigned short e_ovno; // Overlay number
				unsigned short e_res[4]; // Reserved words
				unsigned short e_oemid; // OEM identifier (for e_oeminfo)
				unsigned short e_oeminfo; // OEM information; e_oemid specific
				unsigned short e_res2[10]; // Reserved words
				long           e_lfanew; // File address of new exe header
			};

			struct IMAGE_FILE_HEADER {
				unsigned short Machine;
				unsigned short NumberOfSections;
				unsigned long  TimeDateStamp;
				unsigned long  PointerToSymbolTable;
				unsigned long  NumberOfSymbols;
				unsigned short SizeOfOptionalHeader;
				unsigned short Characteristics;
			};

			struct IMAGE_EXPORT_DIRECTORY {
				unsigned long  Characteristics;
				unsigned long  TimeDateStamp;
				unsigned short MajorVersion;
				unsigned short MinorVersion;
				unsigned long  Name;
				unsigned long  Base;
				unsigned long  NumberOfFunctions;
				unsigned long  NumberOfNames;
				unsigned long  AddressOfFunctions; // RVA from base of image
				unsigned long  AddressOfNames; // RVA from base of image
				unsigned long  AddressOfNameOrdinals; // RVA from base of image
			};

			struct IMAGE_DATA_DIRECTORY {
				unsigned long VirtualAddress;
				unsigned long Size;
			};

			struct IMAGE_OPTIONAL_HEADER64 {
				unsigned short       Magic;
				unsigned char        MajorLinkerVersion;
				unsigned char        MinorLinkerVersion;
				unsigned long        SizeOfCode;
				unsigned long        SizeOfInitializedData;
				unsigned long        SizeOfUninitializedData;
				unsigned long        AddressOfEntryPoint;
				unsigned long        BaseOfCode;
				unsigned long long   ImageBase;
				unsigned long        SectionAlignment;
				unsigned long        FileAlignment;
				unsigned short       MajorOperatingSystemVersion;
				unsigned short       MinorOperatingSystemVersion;
				unsigned short       MajorImageVersion;
				unsigned short       MinorImageVersion;
				unsigned short       MajorSubsystemVersion;
				unsigned short       MinorSubsystemVersion;
				unsigned long        Win32VersionValue;
				unsigned long        SizeOfImage;
				unsigned long        SizeOfHeaders;
				unsigned long        CheckSum;
				unsigned short       Subsystem;
				unsigned short       DllCharacteristics;
				unsigned long long   SizeOfStackReserve;
				unsigned long long   SizeOfStackCommit;
				unsigned long long   SizeOfHeapReserve;
				unsigned long long   SizeOfHeapCommit;
				unsigned long        LoaderFlags;
				unsigned long        NumberOfRvaAndSizes;
				IMAGE_DATA_DIRECTORY DataDirectory[16];
			};

			struct IMAGE_OPTIONAL_HEADER32 {
				unsigned short       Magic;
				unsigned char        MajorLinkerVersion;
				unsigned char        MinorLinkerVersion;
				unsigned long        SizeOfCode;
				unsigned long        SizeOfInitializedData;
				unsigned long        SizeOfUninitializedData;
				unsigned long        AddressOfEntryPoint;
				unsigned long        BaseOfCode;
				unsigned long        BaseOfData;
				unsigned long        ImageBase;
				unsigned long        SectionAlignment;
				unsigned long        FileAlignment;
				unsigned short       MajorOperatingSystemVersion;
				unsigned short       MinorOperatingSystemVersion;
				unsigned short       MajorImageVersion;
				unsigned short       MinorImageVersion;
				unsigned short       MajorSubsystemVersion;
				unsigned short       MinorSubsystemVersion;
				unsigned long        Win32VersionValue;
				unsigned long        SizeOfImage;
				unsigned long        SizeOfHeaders;
				unsigned long        CheckSum;
				unsigned short       Subsystem;
				unsigned short       DllCharacteristics;
				unsigned long        SizeOfStackReserve;
				unsigned long        SizeOfStackCommit;
				unsigned long        SizeOfHeapReserve;
				unsigned long        SizeOfHeapCommit;
				unsigned long        LoaderFlags;
				unsigned long        NumberOfRvaAndSizes;
				IMAGE_DATA_DIRECTORY DataDirectory[16];
			};

			struct IMAGE_NT_HEADERS {
				unsigned long     Signature;
				IMAGE_FILE_HEADER FileHeader;
#ifdef _WIN64
				IMAGE_OPTIONAL_HEADER64 OptionalHeader;
#else
				IMAGE_OPTIONAL_HEADER32 OptionalHeader;
#endif
			};

		} // namespace win

		struct forwarded_hashes {
			unsigned module_hash;
			unsigned function_hash;
		};

		// 64 bit integer where 32 bits are used for the hash offset
		// and remaining 32 bits are used for the hash computed using it
		using offset_hash_pair = unsigned long long;

		LAZY_IMPORTER_FORCEINLINE constexpr unsigned get_hash(offset_hash_pair pair) noexcept { return (pair & 0xFFFFFFFF); }

		LAZY_IMPORTER_FORCEINLINE constexpr unsigned get_offset(offset_hash_pair pair) noexcept { return static_cast<unsigned>(pair >> 32); }

		template<bool CaseSensitive = LAZY_IMPORTER_CASE_SENSITIVITY>
		LAZY_IMPORTER_FORCEINLINE constexpr unsigned hash_single(unsigned value, char c) noexcept
		{
			return (value ^ static_cast<unsigned>((!CaseSensitive && c >= 'A' && c <= 'Z') ? (c | (1 << 5)) : c)) * 323;
		}

		LAZY_IMPORTER_FORCEINLINE constexpr unsigned
			khash_impl(const char* str, unsigned value) noexcept
		{
			return (*str ? khash_impl(str + 1, hash_single(value, *str)) : value);
		}

		LAZY_IMPORTER_FORCEINLINE constexpr offset_hash_pair khash(
			const char* str, unsigned offset) noexcept
		{
			return ((offset_hash_pair{ offset } << 32) | khash_impl(str, offset));
		}

		template<class CharT = char>
		LAZY_IMPORTER_FORCEINLINE unsigned hash(const CharT* str, unsigned offset) noexcept
		{
			unsigned value = offset;

			for (;;) {
				char c = *str++;
				if (!c)
					return value;
				value = hash_single(value, c);
			}
		}

		LAZY_IMPORTER_FORCEINLINE unsigned hash(
			const win::UNICODE_STRING_T& str, unsigned offset) noexcept
		{
			auto       first = str.Buffer;
			const auto last = first + (str.Length / sizeof(wchar_t));
			auto       value = offset;
			for (; first != last; ++first)
				value = hash_single(value, static_cast<char>(*first));

			return value;
		}

		// some helper functions
		LAZY_IMPORTER_FORCEINLINE const win::PEB_T* peb() noexcept
		{

			return reinterpret_cast<const win::PEB_T*>(__readgsqword(0x60));
		}

		LAZY_IMPORTER_FORCEINLINE const win::PEB_LDR_DATA_T* ldr()
		{
			return reinterpret_cast<const win::PEB_LDR_DATA_T*>(peb()->Ldr);
		}

		LAZY_IMPORTER_FORCEINLINE const win::IMAGE_NT_HEADERS* nt_headers(
			const char* base) noexcept
		{
			return reinterpret_cast<const win::IMAGE_NT_HEADERS*>(
				base + reinterpret_cast<const win::IMAGE_DOS_HEADER*>(base)->e_lfanew);
		}

		LAZY_IMPORTER_FORCEINLINE const win::IMAGE_EXPORT_DIRECTORY* image_export_dir(
			const char* base) noexcept
		{
			return reinterpret_cast<const win::IMAGE_EXPORT_DIRECTORY*>(
				base + nt_headers(base)->OptionalHeader.DataDirectory->VirtualAddress);
		}

		LAZY_IMPORTER_FORCEINLINE const win::LDR_DATA_TABLE_ENTRY_T* ldr_data_entry() noexcept
		{
			return reinterpret_cast<const win::LDR_DATA_TABLE_ENTRY_T*>(
				ldr()->InLoadOrderModuleList.Flink);
		}

		struct exports_directory {
			unsigned long                      _ied_size;
			const char* _base;
			const win::IMAGE_EXPORT_DIRECTORY* _ied;

		public:
			using size_type = unsigned long;

			LAZY_IMPORTER_FORCEINLINE
				exports_directory(const char* base) noexcept : _base(base)
			{
				const auto ied_data_dir = nt_headers(base)->OptionalHeader.DataDirectory[0];
				_ied = reinterpret_cast<const win::IMAGE_EXPORT_DIRECTORY*>(
					base + ied_data_dir.VirtualAddress);
				_ied_size = ied_data_dir.Size;
			}

			LAZY_IMPORTER_FORCEINLINE explicit operator bool() const noexcept
			{
				return reinterpret_cast<const char*>(_ied) != _base;
			}

			LAZY_IMPORTER_FORCEINLINE size_type size() const noexcept
			{
				return _ied->NumberOfNames;
			}

			LAZY_IMPORTER_FORCEINLINE const char* base() const noexcept { return _base; }
			LAZY_IMPORTER_FORCEINLINE const win::IMAGE_EXPORT_DIRECTORY* ied() const noexcept
			{
				return _ied;
			}

			LAZY_IMPORTER_FORCEINLINE const char* name(size_type index) const noexcept
			{
				return _base + reinterpret_cast<const unsigned long*>(_base + _ied->AddressOfNames)[index];
			}

			LAZY_IMPORTER_FORCEINLINE const char* address(size_type index) const noexcept
			{
				const auto* const rva_table =
					reinterpret_cast<const unsigned long*>(_base + _ied->AddressOfFunctions);

				const auto* const ord_table = reinterpret_cast<const unsigned short*>(
					_base + _ied->AddressOfNameOrdinals);

				return _base + rva_table[ord_table[index]];
			}

			LAZY_IMPORTER_FORCEINLINE bool is_forwarded(
				const char* export_address) const noexcept
			{
				const auto ui_ied = reinterpret_cast<const char*>(_ied);
				return (export_address > ui_ied && export_address < ui_ied + _ied_size);
			}
		};

		struct safe_module_enumerator {
			using value_type = const detail::win::LDR_DATA_TABLE_ENTRY_T;
			value_type* value;
			value_type* head;

			LAZY_IMPORTER_FORCEINLINE safe_module_enumerator() noexcept
				: safe_module_enumerator(ldr_data_entry())
			{
			}

			LAZY_IMPORTER_FORCEINLINE
				safe_module_enumerator(const detail::win::LDR_DATA_TABLE_ENTRY_T* ldr) noexcept
				: value(ldr->load_order_next()), head(value)
			{
			}

			LAZY_IMPORTER_FORCEINLINE bool next() noexcept
			{
				value = value->load_order_next();

				return value != head && value->DllBase;
			}
		};

		struct unsafe_module_enumerator {
			using value_type = const detail::win::LDR_DATA_TABLE_ENTRY_T*;
			value_type value;

			LAZY_IMPORTER_FORCEINLINE unsafe_module_enumerator() noexcept
				: value(ldr_data_entry())
			{
			}

			LAZY_IMPORTER_FORCEINLINE void reset() noexcept { value = ldr_data_entry(); }

			LAZY_IMPORTER_FORCEINLINE bool next() noexcept
			{
				value = value->load_order_next();
				return true;
			}
		};

		// provides the cached functions which use Derive classes methods
		template<class Derived, class DefaultType = void*>
		class lazy_base {
		protected:
			// This function is needed because every templated function
			// with different args has its own static buffer
			LAZY_IMPORTER_FORCEINLINE static void*& _cache() noexcept
			{
				static void* value = nullptr;
				return value;
			}

		public:
			template<class T = DefaultType>
			LAZY_IMPORTER_FORCEINLINE static T safe() noexcept
			{
				return Derived::template get<T, safe_module_enumerator>();
			}

			template<class T = DefaultType, class Enum = unsafe_module_enumerator>
			LAZY_IMPORTER_FORCEINLINE static T cached() noexcept
			{
				auto& cached = _cache();
				if (!cached)
					cached = Derived::template get<void*, Enum>();

				return (T)(cached);
			}

			template<class T = DefaultType>
			LAZY_IMPORTER_FORCEINLINE static T safe_cached() noexcept
			{
				return cached<T, safe_module_enumerator>();
			}
		};

		template<offset_hash_pair OHP>
		struct lazy_module : lazy_base<lazy_module<OHP>> {
			template<class T = void*, class Enum = unsafe_module_enumerator>
			LAZY_IMPORTER_FORCEINLINE static T get() noexcept
			{
				Enum e;
				do {
					if (hash(e.value->BaseDllName, get_offset(OHP)) == get_hash(OHP))
						return (T)(e.value->DllBase);
				} while (e.next());
				return {};
			}

			template<class T = void*, class Ldr>
			LAZY_IMPORTER_FORCEINLINE static T in(Ldr ldr) noexcept
			{
				safe_module_enumerator e(reinterpret_cast<const detail::win::LDR_DATA_TABLE_ENTRY_T*>(ldr));
				do {
					if (hash(e.value->BaseDllName, get_offset(OHP)) == get_hash(OHP))
						return (T)(e.value->DllBase);
				} while (e.next());
				return {};
			}

		};

		template<offset_hash_pair OHP, class T>
		struct lazy_function : lazy_base<lazy_function<OHP, T>, T> {
			using base_type = lazy_base<lazy_function<OHP, T>, T>;

			template<class... Args>
			LAZY_IMPORTER_FORCEINLINE decltype(auto) operator()(Args&&... args) const
			{
#ifndef LAZY_IMPORTER_CACHE_OPERATOR_PARENS
				return get()(LAZY_IMPORTER_CPP_FORWARD(Args, args)...);
#else
				return this->cached()(LAZY_IMPORTER_CPP_FORWARD(Args, args)...);
#endif
			}

			template<class F = T, class Enum = unsafe_module_enumerator>
			LAZY_IMPORTER_FORCEINLINE static F get() noexcept
			{
				// for backwards compatability.
				// Before 2.0 it was only possible to resolve forwarded exports when
				// this macro was enabled
#ifdef LAZY_IMPORTER_RESOLVE_FORWARDED_EXPORTS
				return forwarded<F, Enum>();
#else

				Enum e;

				do {
#ifdef LAZY_IMPORTER_HARDENED_MODULE_CHECKS
					if (!e.value->DllBase || !e.value->FullDllName.Length)
						continue;
#endif

					const exports_directory exports(e.value->DllBase);

					if (exports) {
						auto export_index = exports.size();
						while (export_index--)
							if (hash(exports.name(export_index), get_offset(OHP)) == get_hash(OHP))
								return (F)(exports.address(export_index));
					}
				} while (e.next());
				return {};
#endif
			}

		};

	}
}
