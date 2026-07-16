<?php $uri1 = $this->uri->segment(1); ?>

<div class="flex flex-col md:flex-row min-h-screen bg-gray-50">

    <!-- SIDEBAR -->
    <aside id="sidebar"
        class="w-64 bg-white shadow-xl md:rounded-r-3xl p-5 md:p-6 flex flex-col
               fixed md:static inset-y-0 left-0 z-40 transform 
               -translate-x-full md:translate-x-0
               transition-transform duration-300 ease-in-out">

        <!-- LOGO -->
        <h1 class="text-lg md:text-xl text-center font-bold text-blue-600 mb-6 md:mb-10 leading-tight">
            IT Support<br>
            <span class="bg-gradient-to-r from-blue-500 to-purple-600 bg-clip-text text-transparent">
                Braja Mukti Cakra
            </span>
        </h1>

        <!-- MENU -->
        <nav class="flex flex-col gap-2 text-sm md:text-base">

            <a href="<?= base_url('dashboard'); ?>"
                class="transition px-3 py-2 rounded-lg font-medium
                <?= $uri1 == 'dashboard'
                    ? 'bg-blue-100 text-blue-600'
                    : 'text-gray-700 hover:bg-gray-100 hover:text-blue-600' ?>">
                Dashboard
            </a>

            <a href="<?= base_url('bantuan'); ?>"
                class="transition px-3 py-2 rounded-lg font-medium
                <?= $uri1 == 'bantuan'
                    ? 'bg-blue-100 text-blue-600'
                    : 'text-gray-700 hover:bg-gray-100 hover:text-blue-600' ?>">
                Bantuan
            </a>

            <a href="<?= base_url('inventorybarang'); ?>"
                class="transition px-3 py-2 rounded-lg font-medium
                <?= $uri1 == 'inventorybarang'
                    ? 'bg-blue-100 text-blue-600'
                    : 'text-gray-700 hover:bg-gray-100 hover:text-blue-600' ?>">
                Inventory Barang
            </a>

        </nav>
    </aside>

    <!-- OVERLAY MOBILE -->
    <div id="overlay"
        class="fixed inset-0 bg-black/40 z-30 hidden md:hidden"></div>

    <!-- MAIN CONTENT -->
    <main class="flex-1 p-4 md:p-10 overflow-x-hidden">

        <!-- HEADER -->
        <div class="flex items-center justify-between mb-6 md:mb-10">

            <!-- HAMBURGER (mobile only) -->
            <button id="hamburgerBtn"
                class="md:hidden p-2 rounded-lg bg-white shadow hover:bg-gray-100 transition">
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>