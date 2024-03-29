<x-app-layout>
    <div class="h-screen w-full flex flex-col justify-center items-center" x-data="">
        <h1 class="text-9xl font-extrabold text-white tracking-widest">404</h1>
        <div class="bg-[#FF6A3D] px-2 text-sm rounded rotate-12 absolute text-black">
            Page Not Found
        </div>
        <button class="mt-5" @click="window.location.replace('/')">
            <div class="relative inline-block text-sm font-medium text-[#FF6A3D] group active:text-orange-500 focus:outline-none focus:ring">
                <span class="absolute inset-0 transition-transform translate-x-0.5 translate-y-0.5 bg-[#FF6A3D] group-hover:translate-y-0 group-hover:translate-x-0"></span>
                <span class="relative block px-8 py-3 bg-zinc-900 border border-current">
                    Go Home
                </span>
            </div>
        </button>
    </div>
</x-app-layout>