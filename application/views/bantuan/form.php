     <!-- CONTENT -->
     <div class="py-4 md:py-6 max-w-6xl w-full grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">

         <!-- LEFT FORM -->
         <div class="bg-[#0f0f0f] p-5 md:p-8 rounded-2xl relative overflow-hidden text-white shadow-xl">
             <h2 class="font-bold text-xl md:text-2xl mb-4 md:mb-6">Form Bantuan IT Support</h2>

             <!-- GRID PATTERN -->
             <div class="absolute inset-0 opacity-10 pointer-events-none"
                 style="background-image: linear-gradient(#ffffff33 1px, transparent 1px),
                           linear-gradient(90deg, #ffffff33 1px, transparent 1px);
                           background-size: 40px 40px;">
             </div>

             <div class="relative z-10 space-y-4 md:space-y-5">

                 <form id="forIT" enctype="multipart/form-data">

                     <label class="text-sm text-gray-300">Nama Karyawan</label>
                     <select id="selectKaryawan" name="NONIK"
                         class="w-full px-3 md:px-4 py-3 rounded-xl bg-white/10 text-white border border-white/20 text-sm md:text-base">
                         <?php foreach ($karyawan as $employee) : ?>
                             <option value="<?= $employee['NONIK'] ?>" class="text-black">
                                 <?= $employee['NM_KAR'] ?>
                             </option>
                         <?php endforeach; ?>
                     </select>

                     <label class="text-sm text-gray-300">Masalah / Trouble</label>
                     <textarea id="report" name="report" rows="3"
                         placeholder="Masalah yang dialami..."
                         class="w-full px-3 md:px-4 py-3 rounded-xl bg-white/10 text-white border border-white/20 text-sm md:text-base"></textarea>

                     <label class="text-sm text-gray-300">Lokasi</label>
                     <input id="lokasi" name="lokasi" type="text"
                         placeholder="Contoh: Office, Warehouse..."
                         class="w-full px-3 md:px-4 py-3 rounded-xl bg-white/10 text-white border border-white/20 text-sm md:text-base">

                     <!-- Hidden -->
                     <input type="hidden" id="fotoBase64" name="foto">
                     <input type="hidden" id="tanggal" name="tanggal">

                     <div class="">
                         <label class="text-sm text-gray-300">Lampiran (Opsional)</label>
                         <input type="file" name="lampiran"
                             class="w-full px-3 md:px-4 py-3 rounded-xl bg-white/10 text-white border border-white/20 text-sm md:text-base">
                     </div>

                     <button type="submit"
                         class="mt-4 md:mt-6 bg-yellow-300 text-black px-6 py-3 rounded-xl w-full flex items-center justify-center gap-2 shadow-lg hover:bg-yellow-400 transition font-semibold">
                         <div class="w-5 h-5 bg-yellow rounded-full flex justify-center items-center text-yellow-300 text-xs">▶</div>
                         Submit
                     </button>

                 </form>
             </div>
         </div>

         <!-- RIGHT SIDE -->
         <div class="space-y-4 md:space-y-6">

             <div class="space-y-4 md:space-y-6">

                 <!-- CAMERA BOX -->
                 <div class="bg-[#0f0f0f] p-5 md:p-6 rounded-2xl shadow-xl relative overflow-hidden">
                     <h2 class="text-white font-semibold mb-3 md:mb-4 text-lg">Ambil Foto</h2>

                     <!-- CAMERA PREVIEW -->
                     <div id="camera"
                         class="rounded-xl mx-auto overflow-hidden border border-gray-700 shadow-lg bg-black
                    w-full max-w-sm h-auto aspect-video">
                     </div>

                     <!-- BUTTON AMBIL FOTO -->
                     <button onclick="take()"
                         class="mt-4 w-full bg-yellow-400 hover:bg-yellow-500 text-black font-semibold py-3 rounded-xl shadow-lg transition flex items-center justify-center gap-2">
                         <span class="text-lg">📸</span> Ambil Foto
                     </button>

                     <!-- BUTTON SWITCH CAMERA -->
                     <button onclick="switchCamera()"
                         class="absolute top-2 right-2 bg-white/20 hover:bg-white/30 backdrop-blur-lg
                       text-white px-3 py-2 rounded-full shadow-md transition flex items-center justify-center">
                         <span id="switchCameraIcon" class="text-lg">🔄</span>
                     </button>
                 </div>

                 <!-- PREVIEW -->
                 <div id="previewBox"
                     class="hidden bg-white rounded-2xl p-4 shadow-lg">
                     <h2 class="font-semibold text-gray-700 mb-3">Preview Foto</h2>
                     <div id="result"
                         class="rounded-xl overflow-hidden shadow-xl border border-gray-300 bg-gray-100 p-2">
                     </div>
                 </div>

             </div>

         </div>


     </div>

     </main>

     </div>