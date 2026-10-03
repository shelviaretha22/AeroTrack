<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'AeroTrack') }}</title>

    {{-- Google Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    {{-- Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

</head>


<body
    class="font-['Plus_Jakarta_Sans']
           bg-[#F3F6F5]
           text-[#365F65]
           antialiased"
>

<div
    x-data="{ sidebarOpen: false }"
    class="min-h-screen"
>


    {{-- =========================================================
         MOBILE SIDEBAR OVERLAY
    ========================================================== --}}
    <div
        x-show="sidebarOpen"
        x-transition.opacity
        @click="sidebarOpen = false"
        class="fixed inset-0 z-40
               bg-[#294F55]/40
               backdrop-blur-sm
               lg:hidden"
        style="display: none;"
    ></div>



    {{-- =========================================================
         SIDEBAR
    ========================================================== --}}
    <aside
        class="fixed
               inset-y-0 left-0
               z-50
               w-[260px]
               bg-[#365F65]
               text-white
               transform
               transition-transform
               duration-300
               lg:translate-x-0"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    >

        {{-- SIDEBAR INNER --}}
        <div class="flex h-full flex-col">


            {{-- =================================================
                 LOGO
            ================================================== --}}
            <div
                class="flex h-[90px]
                       items-center
                       justify-between
                       px-7
                       border-b
                       border-white/10"
            >

                <a href="{{ route('dashboard') }}"
                   class="flex items-center">

                    <img
                        src="{{ asset('images/aerotrack-logo.png') }}"
                        alt="AeroTrack"
                        class="w-[145px] h-auto
                               object-contain
                               brightness-0 invert"
                    >

                </a>


                {{-- Close Sidebar Mobile --}}
                <button
                    @click="sidebarOpen = false"
                    class="lg:hidden
                           text-white/70
                           hover:text-white"
                >

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-6 h-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M6 18L18 6M6 6l12 12"
                        />

                    </svg>

                </button>

            </div>



            {{-- =================================================
                 SYSTEM LABEL
            ================================================== --}}
            <div class="px-7 pt-6 pb-3">

                <p
                    class="text-[9px]
                           font-bold
                           tracking-[0.2em]
                           uppercase
                           text-[#B8CECA]"
                >
                    Commercial Management
                </p>

                <p
                    class="mt-1
                           text-[11px]
                           text-white/50"
                >
                    Airport Tenant System
                </p>

            </div>



            {{-- =================================================
                 NAVIGATION
            ================================================== --}}
            <nav
                class="flex-1
                       overflow-y-auto
                       px-4
                       py-3"
            >


                {{-- DASHBOARD --}}
                <a
                    href="{{ route('dashboard') }}"
                    class="group flex items-center gap-3
                           px-4 py-3
                           mb-1
                           rounded-xl
                           text-sm
                           font-medium
                           text-white/75
                           hover:bg-white/10
                           hover:text-white
                           transition"
                >

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-[19px] h-[19px] shrink-0"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h4v-6h4v6h4a1 1 0 001-1V10"
                        />

                    </svg>

                    <span>Dashboard</span>

                </a>



                {{-- SECTION LABEL --}}
                <div class="px-4 pt-5 pb-2">

                    <p
                        class="text-[9px]
                               font-bold
                               tracking-[0.18em]
                               uppercase
                               text-white/35"
                    >
                        Management
                    </p>

                </div>



                {{-- TENANT --}}
                <a
                    href="#"
                    class="group flex items-center gap-3
                           px-4 py-3
                           mb-1
                           rounded-xl
                           text-sm
                           font-medium
                           text-white/75
                           hover:bg-white/10
                           hover:text-white
                           transition"
                >

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-[19px] h-[19px] shrink-0"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zM22 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"
                        />

                    </svg>

                    <span>Tenant</span>

                </a>



                {{-- LOKASI --}}
                <a
                    href="#"
                    class="group flex items-center gap-3
                           px-4 py-3
                           mb-1
                           rounded-xl
                           text-sm
                           font-medium
                           text-white/75
                           hover:bg-white/10
                           hover:text-white
                           transition"
                >

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-[19px] h-[19px] shrink-0"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 21s7-5.25 7-12a7 7 0 10-14 0c0 6.75 7 12 7 12z"
                        />

                        <circle
                            cx="12"
                            cy="9"
                            r="2.5"
                        />

                    </svg>

                    <span>Lokasi</span>

                </a>



                {{-- KERJA SAMA --}}
                <a
                    href="#"
                    class="group flex items-center gap-3
                           px-4 py-3
                           mb-1
                           rounded-xl
                           text-sm
                           font-medium
                           text-white/75
                           hover:bg-white/10
                           hover:text-white
                           transition"
                >

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-[19px] h-[19px] shrink-0"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M8 12h8M12 8v8M7 3h10a4 4 0 014 4v10a4 4 0 01-4 4H7a4 4 0 01-4-4V7a4 4 0 014-4z"
                        />

                    </svg>

                    <span>Kerja Sama</span>

                </a>



                {{-- KONTRAK --}}
                <a
                    href="#"
                    class="group flex items-center gap-3
                           px-4 py-3
                           mb-1
                           rounded-xl
                           text-sm
                           font-medium
                           text-white/75
                           hover:bg-white/10
                           hover:text-white
                           transition"
                >

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-[19px] h-[19px] shrink-0"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M7 3h8l4 4v14H7a2 2 0 01-2-2V5a2 2 0 012-2z"
                        />

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M15 3v5h5M9 13h6M9 17h6"
                        />

                    </svg>

                    <span>Kontrak</span>

                </a>



                {{-- AKTIVASI --}}
                <a
                    href="#"
                    class="group flex items-center gap-3
                           px-4 py-3
                           mb-1
                           rounded-xl
                           text-sm
                           font-medium
                           text-white/75
                           hover:bg-white/10
                           hover:text-white
                           transition"
                >

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-[19px] h-[19px] shrink-0"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M13 2L4 14h7l-1 8 9-12h-7l1-8z"
                        />

                    </svg>

                    <span>Aktivasi</span>

                </a>



                {{-- PENDAPATAN --}}
                <a
                    href="#"
                    class="group flex items-center gap-3
                           px-4 py-3
                           mb-1
                           rounded-xl
                           text-sm
                           font-medium
                           text-white/75
                           hover:bg-white/10
                           hover:text-white
                           transition"
                >

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-[19px] h-[19px] shrink-0"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 2v20M17 5.5C16.2 4.55 14.8 4 13 4h-2a4 4 0 000 8h2a4 4 0 010 8h-2c-1.8 0-3.2-.55-4-1.5"
                        />

                    </svg>

                    <span>Pendapatan</span>

                </a>



                {{-- MONITORING --}}
                <a
                    href="#"
                    class="group flex items-center gap-3
                           px-4 py-3
                           mb-1
                           rounded-xl
                           text-sm
                           font-medium
                           text-white/75
                           hover:bg-white/10
                           hover:text-white
                           transition"
                >

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-[19px] h-[19px] shrink-0"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M3 12s3.5-6 9-6 9 6 9 6-3.5 6-9 6-9-6-9-6z"
                        />

                        <circle
                            cx="12"
                            cy="12"
                            r="2.5"
                        />

                    </svg>

                    <span>Monitoring</span>

                </a>



                {{-- LAPORAN --}}
                <a
                    href="#"
                    class="group flex items-center gap-3
                           px-4 py-3
                           mb-1
                           rounded-xl
                           text-sm
                           font-medium
                           text-white/75
                           hover:bg-white/10
                           hover:text-white
                           transition"
                >

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-[19px] h-[19px] shrink-0"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M6 3h12a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V5a2 2 0 012-2z"
                        />

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M8 8h8M8 12h8M8 16h5"
                        />

                    </svg>

                    <span>Laporan</span>

                </a>

            </nav>



            {{-- =================================================
                 BOTTOM SIDEBAR
            ================================================== --}}
            <div class="px-4 pb-5">


                {{-- DIVIDER --}}
                <div class="border-t border-white/10 mb-3"></div>


                {{-- PROFILE --}}
                <a
                    href="#"
                    class="flex items-center gap-3
                           px-4 py-3
                           rounded-xl
                           text-sm
                           text-white/70
                           hover:bg-white/10
                           hover:text-white
                           transition"
                >

                    <div
                        class="w-8 h-8
                               rounded-lg
                               bg-[#B8CECA]/20
                               flex items-center justify-center"
                    >

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-4 h-4"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M20 21a8 8 0 00-16 0M12 13a4 4 0 100-8 4 4 0 000 8z"
                            />

                        </svg>

                    </div>

                    <span>Profile</span>

                </a>



                {{-- LOGOUT --}}
                <form
                    method="POST"
                    action="{{ route('logout') }}"
                >

                    @csrf

                    <button
                        type="submit"
                        class="w-full
                               flex items-center gap-3
                               px-4 py-3
                               rounded-xl
                               text-sm
                               text-white/60
                               hover:bg-white/10
                               hover:text-white
                               transition"
                    >

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-[19px] h-[19px]"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M10 17l5-5-5-5M15 12H3"
                            />

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M21 19V5a2 2 0 00-2-2h-6"
                            />

                        </svg>

                        <span>Logout</span>

                    </button>

                </form>


            </div>

        </div>

    </aside>



    {{-- =========================================================
         MAIN AREA
    ========================================================== --}}
    <div
        class="lg:pl-[260px] min-h-screen"
    >


        {{-- =====================================================
             TOPBAR
        ====================================================== --}}
        <header
            class="h-[76px]
                   bg-white
                   border-b
                   border-[#DCE6E4]
                   flex items-center
                   justify-between
                   px-5 sm:px-7 lg:px-9
                   sticky top-0
                   z-30"
        >


            {{-- LEFT TOPBAR --}}
            <div class="flex items-center gap-4">

                {{-- Mobile Menu --}}
                <button
                    @click="sidebarOpen = true"
                    class="lg:hidden
                           w-10 h-10
                           rounded-xl
                           bg-[#F3F6F5]
                           flex items-center justify-center
                           text-[#365F65]"
                >

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M4 6h16M4 12h16M4 18h16"
                        />

                    </svg>

                </button>


                {{-- Page Title --}}
                <div>

                    <p
                        class="text-[10px]
                               font-semibold
                               uppercase
                               tracking-[0.15em]
                               text-[#8BA5A2]"
                    >
                        AeroTrack
                    </p>

                    <h1
                        class="text-base sm:text-lg
                               font-bold
                               text-[#365F65]"
                    >
                        @yield('page-title', 'Dashboard')
                    </h1>

                </div>

            </div>



            {{-- RIGHT TOPBAR --}}
            <div class="flex items-center gap-3 sm:gap-5">


                {{-- SEARCH --}}
                <div
                    class="hidden md:flex
                           items-center
                           w-[220px]
                           h-10
                           px-3
                           rounded-xl
                           bg-[#F3F6F5]
                           border
                           border-[#E2EBE9]"
                >

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-4 h-4 text-[#8BA5A2]"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >

                        <circle
                            cx="11"
                            cy="11"
                            r="7"
                        />

                        <path
                            stroke-linecap="round"
                            d="M20 20l-3.5-3.5"
                        />

                    </svg>

                    <input
                        type="text"
                        placeholder="Search..."
                        class="ml-2
                               w-full
                               bg-transparent
                               border-0
                               outline-none
                               focus:ring-0
                               text-xs
                               text-[#365F65]
                               placeholder-[#9AAEAB]"
                    >

                </div>



                {{-- NOTIFICATION --}}
                <button
                    class="relative
                           w-10 h-10
                           rounded-xl
                           bg-[#F3F6F5]
                           flex items-center justify-center
                           text-[#527D82]
                           hover:bg-[#E8F0EE]
                           transition"
                >

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M18 8a6 6 0 00-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"
                        />

                    </svg>


                    {{-- Notification Dot --}}
                    <span
                        class="absolute
                               top-2 right-2
                               w-1.5 h-1.5
                               rounded-full
                               bg-[#4F8189]"
                    ></span>

                </button>



                {{-- USER --}}
                <div
                    class="hidden sm:flex
                           items-center gap-3
                           pl-2"
                >

                    <div
                        class="w-9 h-9
                               rounded-xl
                               bg-[#D9E5E3]
                               flex items-center justify-center
                               text-[#365F65]
                               font-bold
                               text-sm"
                    >
                        C
                    </div>

                    <div class="hidden lg:block">

                        <p
                            class="text-xs
                                   font-bold
                                   text-[#365F65]"
                        >
                            Commercial
                        </p>

                        <p
                            class="text-[10px]
                                   text-[#8BA5A2]"
                        >
                            Administrator
                        </p>

                    </div>

                </div>

            </div>

        </header>



        {{-- =====================================================
             PAGE CONTENT
        ====================================================== --}}
        <main
            class="p-5 sm:p-7 lg:p-9"
        >

            @yield('content')

        </main>


    </div>

</div>

</body>

</html>