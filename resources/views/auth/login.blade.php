<x-guest-layout>

<div class="min-h-screen w-full overflow-x-hidden bg-[#F3F6F5]">

    <div class="min-h-screen w-full flex flex-col lg:flex-row">

        {{-- =========================================================
             LEFT SIDE - AIRPORT IMAGE
        ========================================================== --}}
        <div class="relative w-full lg:w-[62%]
                    min-h-[430px] lg:min-h-0 lg:h-screen
                    overflow-hidden">

            {{-- Background Image --}}
            <img
                src="{{ asset('images/airport-bg.jpg') }}"
                alt="Juanda Airport"
                class="absolute inset-0 w-full h-full object-cover"
            >

            {{-- Soft Overall Overlay --}}
            <div class="absolute inset-0 bg-[#3F6970]/35"></div>

            {{-- Left Dark Gradient --}}
            <div class="absolute inset-0 bg-gradient-to-r
                        from-[#294F55]/70
                        via-[#416B70]/38
                        to-transparent">
            </div>

            {{-- Bottom Gradient --}}
            <div class="absolute inset-0 bg-gradient-to-t
                        from-[#294F55]/55
                        via-transparent
                        to-transparent">
            </div>

            {{-- Soft Color Tint --}}
            <div class="absolute inset-0 bg-gradient-to-br
                        from-[#527D82]/15
                        via-transparent
                        to-[#B8CECA]/10">
            </div>

            {{-- Decorative Blur --}}
            <div class="absolute top-5 left-5 lg:left-10
                        w-48 h-32 rounded-full
                        bg-[#294F55]/25 blur-2xl">
            </div>


            {{-- LEFT CONTENT --}}
            <div class="relative z-10 min-h-[430px] lg:h-full
                        flex flex-col
                        px-5 sm:px-8 md:px-10 lg:px-12 xl:px-14
                        py-6 sm:py-8 lg:py-9">

                {{-- TOP --}}
                <div class="flex items-start justify-between">

                    {{-- AeroTrack Logo --}}
                    <img
                        src="{{ asset('images/aerotrack-logo.png') }}"
                        alt="AeroTrack"
                        class="w-32 sm:w-44 lg:w-48 h-auto
                               drop-shadow-[0_4px_12px_rgba(0,0,0,0.35)]"
                    >

                    {{-- Department Badge --}}
                    <div class="hidden sm:flex items-center
                                px-4 py-2
                                rounded-full
                                bg-[#355F65]/55
                                backdrop-blur-md
                                border border-white/15
                                text-white/90
                                text-[10px]
                                font-semibold
                                tracking-[0.18em]">

                        COMMERCIAL MANAGEMENT

                    </div>

                </div>


                {{-- MAIN HERO CONTENT --}}
                <div class="my-auto max-w-[600px]">

                    {{-- Small Label --}}
                    <div class="flex items-center gap-3 mb-5">

                        <div class="w-10 h-[2px] bg-[#B8CECA]"></div>

                        <span class="text-[#E3EEEC]
                                     text-xs sm:text-sm
                                     font-semibold
                                     tracking-[0.18em]
                                     uppercase">

                            Airport Tenant Management

                        </span>

                    </div>


                    {{-- Heading --}}
                    <h1 class="text-white
                               text-3xl sm:text-4xl md:text-5xl lg:text-[54px]
                               leading-[1.05]
                               font-bold
                               tracking-tight
                               drop-shadow-[0_3px_10px_rgba(0,0,0,0.45)]">

                        Manage Your

                        <span class="block text-[#D9E5E3]">
                            Airport Tenant
                        </span>

                        Smarter.

                    </h1>


                    {{-- Description --}}
                    <p class="mt-6 max-w-[520px]
                              text-[#F0F5F4]/90
                              text-sm sm:text-base
                              leading-relaxed
                              drop-shadow-[0_2px_6px_rgba(0,0,0,0.35)]">

                        Satu platform untuk memantau tenant,
                        lokasi, kontrak, aktivasi, dan pendapatan
                        dalam pengelolaan tenant bandara.

                    </p>


                    {{-- FEATURE TAGS --}}
                    <div class="mt-8 flex flex-wrap gap-3">

                        <div class="px-4 py-2 rounded-full
                                    bg-[#365F65]/65
                                    backdrop-blur-md
                                    border border-white/15
                                    text-white
                                    text-xs font-medium">

                            Tenant Monitoring

                        </div>

                        <div class="px-4 py-2 rounded-full
                                    bg-[#365F65]/65
                                    backdrop-blur-md
                                    border border-white/15
                                    text-white
                                    text-xs font-medium">

                            Contract Tracking

                        </div>

                        <div class="px-4 py-2 rounded-full
                                    bg-[#365F65]/65
                                    backdrop-blur-md
                                    border border-white/15
                                    text-white
                                    text-xs font-medium">

                            Revenue Monitoring

                        </div>

                    </div>

                </div>


                {{-- =====================================================
                     BOTTOM INSTITUTION BRANDING
                ====================================================== --}}
                <div class="flex items-end justify-between gap-5">

                    {{-- Institution --}}
                    <div class="flex items-center gap-4">

                        {{-- Angkasa Pura Logo --}}
                        <div class="flex items-center justify-center
                                    w-14 h-14
                                    rounded-xl
                                    bg-white/90
                                    backdrop-blur-sm
                                    shadow-[0_4px_15px_rgba(0,0,0,0.18)]
                                    p-2.5">

                            <img
                                src="{{ asset('images/angkasa-pura-logo.png') }}"
                                alt="PT Angkasa Pura Indonesia"
                                class="max-w-full max-h-full object-contain"
                            >

                        </div>


                        {{-- Institution Text --}}
                        <div class="text-white
                                    drop-shadow-[0_2px_5px_rgba(0,0,0,0.4)]">

                            <p class="text-[9px]
                                      uppercase
                                      tracking-[0.2em]
                                      text-[#D9E5E3]/80
                                      font-semibold">

                                Airport Tenant Management for

                            </p>

                            <p class="mt-1
                                      text-sm
                                      font-semibold">

                                Bandar Udara Internasional Juanda

                            </p>

                            <p class="mt-0.5
                                      text-[11px]
                                      text-[#E3EEEC]/85">

                                PT Angkasa Pura Indonesia

                            </p>

                        </div>

                    </div>


                    {{-- System Label --}}
                    <div class="hidden sm:block
                                text-right
                                text-white/65
                                text-[9px]
                                tracking-[0.18em]
                                uppercase">

                        <p>
                            AEROTRACK SYSTEM
                        </p>

                        <p class="mt-1">
                            2026
                        </p>

                    </div>

                </div>

            </div>

        </div>


        {{-- =========================================================
             RIGHT SIDE - LOGIN
        ========================================================== --}}
        <div class="relative w-full lg:w-[38%]
                    min-h-[600px] lg:min-h-0 lg:h-screen
                    bg-[#F8FAF9]
                    flex items-center justify-center
                    px-5 sm:px-8 md:px-10 lg:px-12
                    py-12 sm:py-14 lg:py-0
                    overflow-hidden">


            {{-- Decorative Circle --}}
            <div class="absolute
                        -top-24 -right-24
                        w-72 h-72
                        rounded-full
                        bg-[#D9E5E3]/55">
            </div>

            <div class="absolute
                        -bottom-28 -left-28
                        w-80 h-80
                        rounded-full
                        bg-[#B8CECA]/20">
            </div>


            {{-- LOGIN CONTENT --}}
            <div class="relative z-10
                        w-full max-w-[410px]">


                {{-- BRAND --}}
                <div class="mb-10">

                    <div class="flex items-center gap-3">

                        <img
                            src="{{ asset('images/aerotrack-logo.png') }}"
                            alt="AeroTrack"
                            class="w-28 sm:w-32 md:w-36 h-auto"
                        >

                    </div>

                    <div class="mt-7">

                        <p class="text-[#4F8189]
                                  text-xs
                                  font-bold
                                  tracking-[0.2em]
                                  uppercase">

                            Welcome Back

                        </p>

                        <h2 class="mt-2
                                   text-2xl sm:text-3xl
                                   font-bold
                                   text-[#365F65]">

                            Sign in to continue

                        </h2>

                        <p class="mt-2
                                  text-sm
                                  text-gray-500">

                            Login untuk mengakses AeroTrack
                            Commercial Management System.

                        </p>

                    </div>

                </div>


                {{-- SESSION STATUS --}}
                <x-auth-session-status
                    class="mb-4"
                    :status="session('status')"
                />


                {{-- LOGIN FORM --}}
                <form method="POST" action="{{ route('login') }}">

                    @csrf


                    {{-- EMAIL --}}
                    <div>

                        <x-input-label
                            for="email"
                            :value="__('Email')"
                            class="text-[#365F65] font-semibold"
                        />

                        <x-text-input
                            id="email"
                            class="block mt-2 w-full
                                   border-[#D5E1DF]
                                   focus:border-[#6E9AA0]
                                   focus:ring-[#6E9AA0]/20
                                   rounded-xl
                                   bg-white"
                            type="email"
                            name="email"
                            :value="old('email')"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="Enter your email"
                        />

                        <x-input-error
                            :messages="$errors->get('email')"
                            class="mt-2"
                        />

                    </div>


                    {{-- PASSWORD --}}
                    <div class="mt-5">

                        <x-input-label
                            for="password"
                            :value="__('Password')"
                            class="text-[#365F65] font-semibold"
                        />

                        <x-text-input
                            id="password"
                            class="block mt-2 w-full
                                   border-[#D5E1DF]
                                   focus:border-[#6E9AA0]
                                   focus:ring-[#6E9AA0]/20
                                   rounded-xl
                                   bg-white"
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            placeholder="Enter your password"
                        />

                        <x-input-error
                            :messages="$errors->get('password')"
                            class="mt-2"
                        />

                    </div>


                    {{-- REMEMBER --}}
                    <div class="flex items-center justify-between mt-5">

                        <label
                            for="remember_me"
                            class="inline-flex items-center"
                        >

                            <input
                                id="remember_me"
                                type="checkbox"
                                class="rounded
                                       border-gray-300
                                       text-[#4F8189]
                                       shadow-sm
                                       focus:ring-[#6E9AA0]"
                                name="remember"
                            >

                            <span class="ms-2
                                         text-sm
                                         text-gray-500">

                                Remember me

                            </span>

                        </label>


                        @if (Route::has('password.request'))

                            <a
                                class="text-sm
                                       text-[#4F8189]
                                       hover:text-[#365F65]
                                       font-medium
                                       transition"
                                href="{{ route('password.request') }}"
                            >

                                Forgot password?

                            </a>

                        @endif

                    </div>


                    {{-- LOGIN BUTTON --}}
                    <div class="mt-7">

                        <button
                            type="submit"
                            class="w-full
                                   py-3.5
                                   rounded-xl
                                   bg-[#4F8189]
                                   hover:bg-[#3F7078]
                                   text-white
                                   font-semibold
                                   text-sm
                                   tracking-wide
                                   transition
                                   duration-200
                                   shadow-lg
                                   shadow-[#4F8189]/20
                                   hover:shadow-[#4F8189]/30"
                        >

                            Sign In

                        </button>

                    </div>

                </form>


                {{-- FOOTER --}}
                <div class="mt-10 pt-6
                            border-t border-[#DCE6E4]">

                    <div class="flex items-center justify-between">

                        <div>

                            <p class="text-[10px]
                                      font-semibold
                                      tracking-[0.16em]
                                      text-[#6E9AA0]
                                      uppercase">

                                AeroTrack

                            </p>

                            <p class="mt-1
                                      text-[10px]
                                      text-gray-400">

                                Airport Tenant Management System

                            </p>

                        </div>


                        <div class="text-right">

                            <p class="text-[10px]
                                      text-gray-400">

                                Juanda Airport

                            </p>

                            <p class="mt-1
                                      text-[10px]
                                      text-gray-400">

                                © 2026

                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

</x-guest-layout>