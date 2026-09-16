<!DOCTYPE html>
<html lang="th">
<head>
    <?php $pageTitle = 'หลังบ้าน - พนักงาน & ครัว | ชาบูหมาล่าบุฟเฟต์'; include __DIR__ . '/partials/head.php'; ?>
    <!-- QRCode.js Library สำหรับสร้าง QR Code จริง -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
</head>
<body class="min-h-screen antialiased flex flex-col">

<div id="app" class="flex-grow flex flex-col" v-cloak>

    <!-- TOP NAVIGATION BAR -->
    <header class="bg-[#191716] border-b border-[#353535] px-4 md:px-6 py-3 sticky top-0 z-50 flex flex-col sm:flex-row gap-3 justify-between items-center shadow-xl">
        <div class="flex items-center gap-3 w-full sm:w-auto justify-between sm:justify-start">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-accent-crimson flex items-center justify-center text-white font-bold text-xl shadow-lg">
                    <i class="fa-solid fa-pepper-hot"></i>
                </div>
                <div>
                    <h1 class="text-base md:text-lg font-bold tracking-wide text-white flex items-center gap-2">
                        ชาบูหมาล่าบุฟเฟต์ <span class="bg-accent-gold/20 text-accent-gold text-xs px-2 py-0.5 rounded border border-accent-gold/40 font-mono">หลังบ้าน</span>
                    </h1>
                    <p class="text-[11px] md:text-xs text-slate-400">ระบบจัดการพนักงาน POS และครัว KDS</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2 md:gap-3 w-full sm:w-auto justify-end">
            <div v-if="staffUnlocked" class="bg-[#121212] p-1 rounded-xl border border-[#353535] flex items-center gap-1 w-full sm:w-auto justify-center">
                <button type="button" @click="switchMode('staff')" :class="currentMode === 'staff' ? 'bg-accent-gold text-black font-semibold shadow' : 'text-slate-400 hover:text-white'" class="flex-1 sm:flex-none px-3 md:px-4 py-1.5 rounded-lg text-xs transition-all flex items-center justify-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-user-tie"></i> พนักงาน POS
                </button>
                <button type="button" @click="switchMode('kitchen')" :class="currentMode === 'kitchen' ? 'bg-accent-crimson text-white font-semibold shadow' : 'text-slate-400 hover:text-white'" class="flex-1 sm:flex-none px-3 md:px-4 py-1.5 rounded-lg text-xs transition-all flex items-center justify-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-fire-burner"></i> ครัว KDS <span v-if="pendingOrdersCount" class="bg-red-500 text-white text-[9px] min-w-4 px-1 py-0.5 rounded-full ml-1">{{ pendingOrdersCount }}</span>
                </button>
            </div>

            <a href="index.php" class="text-xs text-slate-400 hover:text-white bg-[#121212] p-2 md:px-3 md:py-2 rounded-lg border border-[#353535] transition-colors flex items-center gap-1 cursor-pointer shrink-0">
                <i class="fa-solid fa-mobile-screen-button"></i> <span class="hidden md:inline">หน้าลูกค้า</span>
            </a>

            <button v-if="staffUnlocked" type="button" @click="staffLogout" class="text-xs text-slate-400 hover:text-amber-400 bg-[#121212] p-2 md:px-3 md:py-2 rounded-lg border border-[#353535] transition-colors flex items-center gap-1 cursor-pointer shrink-0">
                <i class="fa-solid fa-lock"></i> <span class="hidden md:inline">ล็อกพนักงาน</span>
            </button>

            <button type="button" @click="resetDemoData" class="text-xs text-slate-400 hover:text-red-400 bg-[#121212] p-2 md:px-3 md:py-2 rounded-lg border border-[#353535] transition-colors flex items-center gap-1 cursor-pointer shrink-0">
                <i class="fa-solid fa-rotate-right"></i> <span class="hidden md:inline">รีเซ็ต</span>
            </button>
        </div>
    </header>

    <!-- MAIN APP CONTAINER -->
    <main class="flex-grow p-2 sm:p-4 md:p-6 flex items-center justify-center relative">

        <!-- 🖥️ STAFF & KITCHEN INTERFACE -->
        <div v-if="staffUnlocked" class="w-full max-w-7xl min-h-[80vh] bg-[#121212] rounded-3xl border border-[#353535] shadow-2xl flex flex-col md:flex-row overflow-hidden z-10">

            <!-- SIDEBAR NAVIGATION (STAFF) -->
            <aside v-if="currentMode === 'staff'" class="w-full md:w-64 bg-[#181615] border-r border-[#353535] p-4 flex flex-col justify-between shrink-0">
                <div class="space-y-4">
                    <div class="text-xs font-bold text-slate-400 tracking-wider uppercase">เมนูจัดการ POS</div>
                    <nav class="space-y-1">
                        <button @click="staffTab='dashboard'" :class="staffTab==='dashboard'?'bg-accent-crimson text-white font-bold':'text-slate-300 hover:bg-[#2A2A2A]'" class="w-full text-left px-3 py-2.5 rounded-xl text-xs flex items-center gap-3 transition-colors">
                            <i class="fa-solid fa-chart-line text-sm"></i> แดชบอร์ดภาพรวม
                        </button>
                        <button @click="staffTab='tables'" :class="staffTab==='tables'?'bg-accent-crimson text-white font-bold':'text-slate-300 hover:bg-[#2A2A2A]'" class="w-full text-left px-3 py-2.5 rounded-xl text-xs flex items-center gap-3 transition-colors">
                            <i class="fa-solid fa-border-all text-sm"></i> ผังโต๊ะทั้งหมด
                        </button>
                        <button @click="staffTab='orders'" :class="staffTab==='orders'?'bg-accent-crimson text-white font-bold':'text-slate-300 hover:bg-[#2A2A2A]'" class="w-full text-left px-3 py-2.5 rounded-xl text-xs flex items-center justify-between transition-colors">
                            <span class="flex items-center gap-3"><i class="fa-solid fa-list-check text-sm"></i> ออเดอร์ลูกค้า</span>
                            <span v-if="pendingOrdersCount" class="bg-red-500 text-white text-[9px] px-1.5 py-0.5 rounded-full font-bold">{{ pendingOrdersCount }}</span>
                        </button>
                    </nav>
                </div>

                <div class="bg-[#211E1C] p-3 rounded-2xl border border-[#353535] space-y-2 text-xs">
                    <div class="text-slate-400 font-bold">สรุปสถานะร้าน</div>
                    <div class="flex justify-between text-white"><span>โต๊ะว่าง:</span> <b class="text-green-400">{{ tables.filter(t => t.status==='available').length }} / {{ tables.length }}</b></div>
                    <div class="flex justify-between text-white"><span>มีลูกค้า:</span> <b class="text-accent-gold">{{ tables.filter(t => t.status==='occupied').length }} โต๊ะ</b></div>
                </div>
            </aside>

            <!-- SIDEBAR NAVIGATION (KITCHEN) -->
            <aside v-if="currentMode === 'kitchen'" class="w-full md:w-64 bg-[#181615] border-r border-[#353535] p-4 flex flex-col justify-between shrink-0">
                <div class="space-y-4">
                    <div class="text-xs font-bold text-slate-400 tracking-wider uppercase">ระบบครัว KDS</div>
                    <nav class="space-y-1">
                        <button @click="kitchenTab='orders'" :class="kitchenTab==='orders'?'bg-accent-crimson text-white font-bold':'text-slate-300 hover:bg-[#2A2A2A]'" class="w-full text-left px-3 py-2.5 rounded-xl text-xs flex items-center justify-between transition-colors">
                            <span class="flex items-center gap-3"><i class="fa-solid fa-fire-burner text-sm"></i> ออเดอร์เข้าครัว</span>
                            <span v-if="pendingOrdersCount" class="bg-red-500 text-white text-[9px] px-1.5 py-0.5 rounded-full font-bold">{{ pendingOrdersCount }}</span>
                        </button>
                        <button @click="kitchenTab='stock'" :class="kitchenTab==='stock'?'bg-accent-crimson text-white font-bold':'text-slate-300 hover:bg-[#2A2A2A]'" class="w-full text-left px-3 py-2.5 rounded-xl text-xs flex items-center gap-3 transition-colors">
                            <i class="fa-solid fa-boxes-stacked text-sm"></i> จัดการสต็อกสินค้า (วัตถุดิบ)
                        </button>
                    </nav>
                </div>
            </aside>

            <!-- MAIN CONTENT AREA -->
            <main class="flex-1 p-4 md:p-6 overflow-y-auto bg-[#0D0D0D]">

                <!-- 0. STAFF: DASHBOARD -->
                <div v-if="currentMode==='staff' && staffTab==='dashboard'" class="space-y-6">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl font-bold text-white flex items-center gap-2">
                            <i class="fa-solid fa-chart-line text-accent-gold"></i> แดชบอร์ดภาพรวมธุรกิจ (Business Dashboard)
                        </h2>
                        <span class="text-xs text-slate-400 bg-[#1B1B1B] px-3 py-1.5 rounded-xl border border-[#353535]">
                            <i class="fa-regular fa-clock mr-1"></i> อัปเดตแบบเรียลไทม์
                        </span>
                    </div>

                    <!-- Statistics Cards Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-[#1B1B1B] border border-[#353535] rounded-2xl p-4 flex items-center justify-between">
                            <div>
                                <div class="text-xs text-slate-400">ยอดขายรวมโดยประมาณ</div>
                                <div class="text-2xl font-black text-accent-gold mt-1">{{ formatNumber(dashboardTotalRevenue) }} <small class="text-xs">฿</small></div>
                            </div>
                            <div class="w-12 h-12 rounded-2xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-amber-400 text-xl">
                                <i class="fa-solid fa-wallet"></i>
                            </div>
                        </div>

                        <div class="bg-[#1B1B1B] border border-[#353535] rounded-2xl p-4 flex items-center justify-between">
                            <div>
                                <div class="text-xs text-slate-400">โต๊ะที่ใช้งานอยู่</div>
                                <div class="text-2xl font-black text-white mt-1">{{ tables.filter(t => t.status==='occupied').length }} <span class="text-xs text-slate-500">/ {{ tables.length }} โต๊ะ</span></div>
                            </div>
                            <div class="w-12 h-12 rounded-2xl bg-red-500/10 border border-red-500/30 flex items-center justify-center text-red-400 text-xl">
                                <i class="fa-solid fa-chair"></i>
                            </div>
                        </div>

                        <div class="bg-[#1B1B1B] border border-[#353535] rounded-2xl p-4 flex items-center justify-between">
                            <div>
                                <div class="text-xs text-slate-400">ออเดอร์ค้างทำ (Pending)</div>
                                <div class="text-2xl font-black text-amber-400 mt-1">{{ pendingOrdersCount }} <span class="text-xs text-slate-500">รายการ</span></div>
                            </div>
                            <div class="w-12 h-12 rounded-2xl bg-yellow-500/10 border border-yellow-500/30 flex items-center justify-center text-yellow-400 text-xl">
                                <i class="fa-solid fa-clock-rotate-left"></i>
                            </div>
                        </div>

                        <div class="bg-[#1B1B1B] border border-[#353535] rounded-2xl p-4 flex items-center justify-between">
                            <div>
                                <div class="text-xs text-slate-400">จำนวนถาดที่สั่งทั้งหมด</div>
                                <div class="text-2xl font-black text-emerald-400 mt-1">{{ totalTraysAllTables }} <span class="text-xs text-slate-500">ถาด</span></div>
                            </div>
                            <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-400 text-xl">
                                <i class="fa-solid fa-bowl-food"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Detailed Analytics Row -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Table Occupancy Details -->
                        <div class="bg-[#1B1B1B] border border-[#353535] rounded-2xl p-5 space-y-4">
                            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                                <i class="fa-solid fa-circle-nodes text-accent-crimson"></i> สถานะโต๊ะปัจจุบันและแพ็กเกจ
                            </h3>
                            <div class="space-y-2">
                                <div v-for="table in tables" :key="table.id" class="flex items-center justify-between bg-[#121212] p-3 rounded-xl border border-[#2A2A2A]">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs" :class="table.status==='occupied'?'bg-accent-crimson text-white':'bg-emerald-900/60 text-emerald-300'">
                                            {{ table.name }}
                                        </div>
                                        <div>
                                            <div class="text-xs font-bold text-white">โต๊ะ {{ table.name }}</div>
                                            <div class="text-[10px] text-slate-400">{{ table.status==='occupied' ? table.packageName + ` (${table.adults} ท่าน)` : 'ว่าง' }}</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-xs font-bold" :class="table.status==='occupied'?'text-amber-400':'text-emerald-400'">
                                            {{ table.status==='occupied' ? formatNumber((table.adults * table.packagePrice) + (table.children * table.packagePrice * 0.5)) + ' ฿' : '-' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Menu Order Popularity / Quick Actions Summary -->
                        <div class="bg-[#1B1B1B] border border-[#353535] rounded-2xl p-5 space-y-4">
                            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                                <i class="fa-solid fa-fire text-accent-gold"></i> สรุปสถานะออเดอร์ในระบบครัว
                            </h3>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center bg-[#121212] p-3 rounded-xl border border-[#2A2A2A] text-xs">
                                    <span class="text-slate-300">รอดำเนินการ (Pending)</span>
                                    <span class="bg-amber-500/20 text-amber-400 font-bold px-2.5 py-1 rounded-lg border border-amber-500/30">
                                        {{ orders.filter(o => o.status === 'pending').length }} ออเดอร์
                                    </span>
                                </div>
                                <div class="flex justify-between items-center bg-[#121212] p-3 rounded-xl border border-[#2A2A2A] text-xs">
                                    <span class="text-slate-300">กำลังปรุง (Cooking)</span>
                                    <span class="bg-blue-500/20 text-blue-400 font-bold px-2.5 py-1 rounded-lg border border-blue-500/30">
                                        {{ orders.filter(o => o.status === 'cooking').length }} ออเดอร์
                                    </span>
                                </div>
                                <div class="flex justify-between items-center bg-[#121212] p-3 rounded-xl border border-[#2A2A2A] text-xs">
                                    <span class="text-slate-300">เสิร์ฟแล้ว (Served)</span>
                                    <span class="bg-emerald-500/20 text-emerald-400 font-bold px-2.5 py-1 rounded-lg border border-emerald-500/30">
                                        {{ orders.filter(o => o.status === 'served').length }} ออเดอร์
                                    </span>
                                </div>
                                <div class="pt-2">
                                    <button @click="staffTab='orders'" class="w-full bg-[#2A2A2A] hover:bg-[#333] text-white font-bold py-2.5 rounded-xl text-xs transition-colors flex items-center justify-center gap-2">
                                        ดูรายละเอียดออเดอร์ทั้งหมด <i class="fa-solid fa-arrow-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Service Calls (เรียกพนักงานจากลูกค้า) -->
                    <div class="bg-[#1B1B1B] border border-[#353535] rounded-2xl p-5 space-y-3">
                        <h3 class="text-sm font-bold text-white flex items-center gap-2">
                            <i class="fa-solid fa-bell text-accent-crimson"></i> คำเรียกพนักงานที่รอดำเนินการ
                            <span v-if="serviceCalls.length" class="bg-red-500 text-white text-[10px] px-2 py-0.5 rounded-full font-bold">{{ serviceCalls.length }}</span>
                        </h3>
                        <div v-if="serviceCalls.length===0" class="text-center py-6 text-xs text-slate-500">
                            ไม่มีคำเรียกพนักงานในขณะนี้
                        </div>
                        <div v-for="call in serviceCalls" :key="call.id" class="flex items-center justify-between bg-[#121212] p-3 rounded-xl border border-[#2A2A2A] text-xs">
                            <div>
                                <span class="font-bold text-white">โต๊ะ {{ call.table_number }}</span>
                                <span class="text-slate-400 ml-2">{{ call.reason }}</span>
                            </div>
                            <button @click="resolveServiceCall(call.id)" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-3 py-1.5 rounded-lg text-[10px]">
                                รับทราบ
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 1. STAFF: TABLE MANAGEMENT -->
                <div v-if="currentMode==='staff' && staffTab==='tables'" class="space-y-6">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl font-bold text-white flex items-center gap-2">
                            <i class="fa-solid fa-border-all text-accent-gold"></i> ผังโต๊ะและจัดการเวลาทาน
                        </h2>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div v-for="table in tables" :key="table.id" class="bg-[#1B1B1B] border border-[#353535] rounded-2xl p-4 flex flex-col justify-between space-y-3 relative overflow-hidden">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="text-lg font-black text-white">โต๊ะ {{ table.name }}</div>
                                    <div class="text-[10px]" :class="table.status==='occupied'?'text-amber-400':'text-green-400'">
                                        {{ table.status==='occupied' ? table.packageName : 'โต๊ะว่าง' }}
                                    </div>
                                </div>
                                <span :class="table.status==='occupied'?'bg-red-900/60 text-red-300 border-red-700':'bg-emerald-900/60 text-emerald-300 border-emerald-700'" class="text-[9px] px-2 py-0.5 rounded-full font-bold border">
                                    {{ table.status==='occupied'?'มีลูกค้า':'ว่าง' }}
                                </span>
                            </div>

                            <!-- Occupied Info -->
                            <div v-if="table.status==='occupied'" class="space-y-2 text-xs bg-[#24201E] p-2.5 rounded-xl border border-[#443830]">
                                <div class="flex justify-between text-slate-300">
                                    <span>จำนวน:</span>
                                    <b>{{ table.adults }} ใหญ่ / {{ table.children }} เด็ก</b>
                                </div>
                                <div class="flex justify-between items-center text-slate-300">
                                    <span>เวลาเหลือ:</span>
                                    <b class="text-accent-gold font-mono text-sm">{{ formatTimeLeft(table.timeLeft) }}</b>
                                </div>
                                <div class="pt-2 border-t border-[#353535] flex gap-2">
                                    <button @click="openQRModal(table)" class="flex-1 bg-accent-gold hover:bg-yellow-600 text-black font-extrabold py-1.5 rounded-lg text-[10px] flex items-center justify-center gap-1">
                                        <i class="fa-solid fa-qrcode"></i> ดู QR Code
                                    </button>
                                    <button @click="closeTableSession(table)" class="bg-neutral-800 hover:bg-neutral-700 text-slate-300 font-bold px-2 py-1.5 rounded-lg text-[10px]">
                                        เคลียร์
                                    </button>
                                </div>
                            </div>

                            <!-- Open Table Action -->
                            <div v-else class="pt-2">
                                <button @click="openTableModal(table)" class="w-full bg-accent-emerald hover:bg-emerald-700 text-white font-bold py-2 rounded-xl text-xs flex items-center justify-center gap-1">
                                    <i class="fa-solid fa-plus"></i> เปิดโต๊ะใหม่
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. KITCHEN DISPLAY SYSTEM (KDS) & ORDERS -->
                <div v-if="(currentMode==='kitchen' && kitchenTab==='orders') || (currentMode==='staff' && staffTab==='orders')" class="space-y-6">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl font-bold text-white flex items-center gap-2">
                            <i class="fa-solid fa-fire-burner text-accent-crimson"></i> ออเดอร์ครัว / Kitchen Display System
                        </h2>
                    </div>

                    <div v-if="orders.length===0" class="text-center py-16 text-slate-500">
                        <i class="fa-solid fa-circle-check text-4xl mb-2 text-emerald-500 block"></i>
                        ไม่มีออเดอร์ค้างในขณะนี้
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div v-for="ord in orders" :key="ord.id" class="bg-[#1B1B1B] border border-[#353535] rounded-2xl p-4 space-y-3">
                            <div class="flex justify-between items-center border-b border-[#353535] pb-2">
                                <div>
                                    <span class="text-lg font-black text-white">โต๊ะ {{ getTableName(ord.tableId) }}</span>
                                    <span class="text-[10px] text-slate-400 block">#{{ ord.id }} • {{ ord.time }}</span>
                                </div>
                                <span :class="getStatusBadgeClass(ord.status)" class="text-xs px-2.5 py-1 rounded-full font-bold">
                                    {{ getStatusText(ord.status) }}
                                </span>
                            </div>

                            <div class="space-y-2">
                                <div v-for="item in ord.items" :key="item.id" class="flex justify-between items-center text-xs text-slate-200">
                                    <div>
                                        <div class="font-bold text-white">{{ item.name }}</div>
                                        <div v-if="item.note" class="text-[10px] text-amber-400">** {{ item.note }}</div>
                                    </div>
                                    <span class="bg-red-950/80 text-red-400 border border-red-800 font-black px-2 py-1 rounded-lg text-sm">
                                        {{ item.qty }} ถาด
                                    </span>
                                </div>
                            </div>

                            <div class="pt-2 border-t border-[#353535] flex gap-2">
                                <button v-if="ord.status==='pending'" @click="updateOrderStatus(ord.id, 'cooking')" class="flex-1 bg-amber-600 hover:bg-amber-700 text-white font-bold py-2 rounded-xl text-xs">
                                    กำลังปรุง
                                </button>
                                <button v-if="ord.status==='cooking'" @click="updateOrderStatus(ord.id, 'served')" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 rounded-xl text-xs">
                                    เสิร์ฟแล้ว
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. MENU STOCK MANAGEMENT -->
                <div v-if="currentMode==='kitchen' && kitchenTab==='stock'" class="space-y-6">
                    <h2 class="text-xl font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-boxes-stacked text-accent-gold"></i> จัดการสถานะสต็อกสินค้า (ครัว)
                    </h2>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <div v-for="item in menuItems" :key="item.id" class="bg-[#1B1B1B] border border-[#353535] p-3 rounded-xl flex items-center justify-between">
                            <div>
                                <div class="text-xs font-bold text-white">{{ item.emoji }} {{ item.name }}</div>
                                <div class="text-[10px] text-slate-400">{{ item.category }}</div>
                            </div>
                            <button @click="toggleStock(item)" :class="item.inStock?'bg-emerald-600 hover:bg-emerald-700':'bg-red-600 hover:bg-red-700'" class="text-white text-[10px] font-bold px-2.5 py-1 rounded-lg transition-colors cursor-pointer">
                                {{ item.inStock ? 'มีของ' : 'หมด' }}
                            </button>
                        </div>
                    </div>
                </div>

            </main>
        </div>

    </main>

    <!-- MODAL 0: STAFF PIN GATE -->
    <div v-if="showPinModal" class="fixed inset-0 bg-black/80 flex items-center justify-center p-4 z-50">
        <div class="bg-[#1B1B1B] border border-[#353535] rounded-3xl p-6 max-w-sm w-full space-y-4">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-lock text-accent-gold"></i> ใส่ PIN พนักงาน
            </h3>
            <p class="text-xs text-slate-400">กรุณาใส่ PIN เพื่อเข้าใช้งานหน้าพนักงาน/ครัว</p>

            <input type="password" inputmode="numeric" v-model="pinInput" @keyup.enter="submitPin" placeholder="PIN" autofocus
                   class="w-full bg-[#2A2A2A] border border-[#444] rounded-xl p-3 text-center text-lg tracking-widest text-white outline-none">

            <p v-if="pinError" class="text-xs text-red-400 text-center">{{ pinError }}</p>

            <div class="flex gap-2 pt-2">
                <button @click="submitPin" class="flex-1 bg-accent-crimson hover:bg-red-700 text-white font-bold py-2.5 rounded-xl text-xs">
                    ยืนยัน
                </button>
                <a href="index.php" class="bg-neutral-700 text-slate-300 font-bold px-4 py-2.5 rounded-xl text-xs flex items-center justify-center">
                    ยกเลิก
                </a>
            </div>
        </div>
    </div>

    <!-- MODAL 1: OPEN TABLE SESSION -->
    <div v-if="showOpenModal" class="fixed inset-0 bg-black/80 flex items-center justify-center p-4 z-50">
        <div class="bg-[#1B1B1B] border border-[#353535] rounded-3xl p-6 max-w-md w-full space-y-4">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-chair text-accent-gold"></i> เปิดโต๊ะ {{ selectedModalTable?.name }}
            </h3>

            <div class="space-y-3">
                <div>
                    <label class="text-xs text-slate-300 block mb-1">เลือกแพ็กเกจบุฟเฟต์</label>
                    <select v-model="modalPkg" class="w-full bg-[#2A2A2A] border border-[#444] rounded-xl p-2.5 text-xs text-white outline-none">
                        <option value="299">Standard Mala (299 THB)</option>
                        <option value="399">Premium Pork & Beef (399 THB)</option>
                        <option value="499">Seafood & Wagyu Supreme (499 THB)</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs text-slate-300 block mb-1">ผู้ใหญ่ (คน)</label>
                        <input type="number" min="1" v-model.number="modalAdults" class="w-full bg-[#2A2A2A] border border-[#444] rounded-xl p-2.5 text-xs text-white">
                    </div>
                    <div>
                        <label class="text-xs text-slate-300 block mb-1">เด็ก (คน)</label>
                        <input type="number" min="0" v-model.number="modalChildren" class="w-full bg-[#2A2A2A] border border-[#444] rounded-xl p-2.5 text-xs text-white">
                    </div>
                </div>
            </div>

            <div class="flex gap-2 pt-2">
                <button @click="confirmOpenTable" class="flex-1 bg-accent-crimson hover:bg-red-700 text-white font-bold py-2.5 rounded-xl text-xs">
                    ยืนยันเปิดโต๊ะ
                </button>
                <button @click="showOpenModal=false" class="bg-neutral-700 text-slate-300 font-bold px-4 py-2.5 rounded-xl text-xs">
                    ยกเลิก
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL 2: DISPLAY QR CODE FOR CUSTOMER PHONE SCANNING -->
    <div v-if="showQRModal" class="fixed inset-0 bg-black/80 flex items-center justify-center p-4 z-50">
        <div class="bg-[#1B1B1B] border border-[#353535] rounded-3xl p-6 max-w-sm w-full space-y-4 text-center">
            <h3 class="text-lg font-bold text-white flex items-center justify-center gap-2">
                <i class="fa-solid fa-qrcode text-accent-gold"></i> QR Code โต๊ะ {{ selectedQRTable?.name }}
            </h3>
            <p class="text-xs text-slate-300">
                แพ็กเกจ: <b class="text-amber-400">{{ selectedQRTable?.packageName }}</b>
            </p>

            <!-- QR Container generated dynamically -->
            <div class="bg-white p-4 rounded-2xl inline-block shadow-lg my-2">
                <div id="qrcode-container" class="flex justify-center"></div>
            </div>

            <div class="text-[11px] text-slate-300 bg-[#121212] p-2.5 rounded-xl border border-[#333] break-all font-mono">
                {{ getQRUrl(selectedQRTable?.id) }}
            </div>

            <p class="text-[11px] text-slate-400 leading-relaxed">
                📱 ใช้กล้องมือถือสแกนเพื่อสั่งอาหาร<br>
                <span class="text-amber-400 text-[10px]">*ต้องเชื่อมต่อ Wi-Fi เดียวกันกับเครื่อง Server (XAMPP)</span>
            </p>

            <div class="flex flex-col gap-2 pt-2">
                <button @click="demoCustomerView(selectedQRTable)" class="w-full bg-accent-crimson hover:bg-red-700 text-white font-bold py-2.5 rounded-xl text-xs flex items-center justify-center gap-2">
                    <i class="fa-solid fa-mobile-screen"></i> เปิดดูหน้ามือถือลูกค้า (แท็บใหม่)
                </button>
                <button @click="showQRModal=false" class="w-full bg-neutral-800 text-slate-300 font-bold py-2.5 rounded-xl text-xs">
                    ปิดหน้าต่าง
                </button>
            </div>
        </div>
    </div>

</div>

<!-- SHARED APP METHODS -->
<script src="assets/shared.js"></script>

<!-- VUE 3 APP SCRIPT -->
<script>
    const { createApp } = Vue

    createApp({
        data() {
            return {
                currentMode: 'staff',
                staffTab: 'dashboard',
                kitchenTab: 'orders',

                showOpenModal: false,
                selectedModalTable: null,
                modalPkg: '399',
                modalAdults: 2,
                modalChildren: 0,

                showQRModal: false,
                selectedQRTable: null,

                menuItems: [],
                tables: [],
                serviceCalls: [],
                orders: [],

                staffUnlocked: false,
                showPinModal: false,
                pinInput: '',
                pinError: '',

                timer: null,
                pollTimer: null
            }
        },
        computed: {
            pendingOrdersCount() {
                return this.orders.filter(o => o.status === 'pending').length
            },
            dashboardTotalRevenue() {
                return this.tables.reduce((sum, t) => {
                    if (t.status === 'occupied') {
                        const adultTotal = (t.adults || 0) * (t.packagePrice || 0)
                        const childTotal = (t.children || 0) * (t.packagePrice || 0) * 0.5
                        return sum + adultTotal + childTotal
                    }
                    return sum
                }, 0)
            },
            totalTraysAllTables() {
                return this.orders.reduce((sum, ord) => sum + ord.items.reduce((iSum, i) => iSum + i.qty, 0), 0)
            }
        },
        methods: {
            ...SharedAppMethods,
            switchMode(mode) {
                this.currentMode = mode
            },
            async submitPin() {
                try {
                    const data = await this.apiPost('api/staff_login.php', { pin: this.pinInput })
                    if (data.status !== 'success') { this.pinError = data.message || 'PIN ไม่ถูกต้อง'; return }

                    this.staffUnlocked = true
                    sessionStorage.setItem('staffUnlocked', '1')
                    this.showPinModal = false
                    await this.initStaffData()
                } catch (e) {
                    this.pinError = 'เชื่อมต่อเซิร์ฟเวอร์ไม่สำเร็จ'
                }
            },
            async staffLogout() {
                try { await this.apiPost('api/staff_logout.php', {}) } catch (e) {}
                this.staffUnlocked = false
                sessionStorage.removeItem('staffUnlocked')
                if (this.timer) clearInterval(this.timer)
                if (this.pollTimer) clearInterval(this.pollTimer)
                window.location.href = 'index.php'
            },
            handleAuthExpired() {
                if (this.staffUnlocked) {
                    this.staffUnlocked = false
                    sessionStorage.removeItem('staffUnlocked')
                    this.showPinModal = true
                    if (this.pollTimer) clearInterval(this.pollTimer)
                }
            },
            getTableName(tableId) {
                const t = this.tables.find(tbl => tbl.id === tableId)
                return t ? t.name : tableId
            },
            openTableModal(table) {
                this.selectedModalTable = table
                this.showOpenModal = true
            },
            async confirmOpenTable() {
                if (!this.selectedModalTable) return
                const tableId = this.selectedModalTable.id
                try {
                    const data = await this.apiPost('api/open_session.php', {
                        table_id: tableId,
                        package_id: this.modalPkg,
                        adults: this.modalAdults,
                        children: this.modalChildren
                    })
                    if (data.status !== 'success') { alert(data.message || 'เปิดโต๊ะไม่สำเร็จ'); return }

                    await this.fetchTables()
                    this.showOpenModal = false
                    const openedTable = this.tables.find(t => t.id === tableId)
                    if (openedTable) this.openQRModal(openedTable)
                } catch (e) {
                    alert('เชื่อมต่อเซิร์ฟเวอร์ไม่สำเร็จ')
                }
            },
            getQRUrl(tableId) {
                if (!tableId) return ''
                const customerPath = window.location.pathname.replace(/staff\.php$/, 'index.php')
                return `${window.location.origin}${customerPath}?table=${tableId}`
            },
            openQRModal(table) {
                this.selectedQRTable = table
                this.showQRModal = true

                this.$nextTick(() => {
                    const container = document.getElementById("qrcode-container")
                    if (container) {
                        container.innerHTML = ""
                        new QRCode(container, {
                            text: this.getQRUrl(table.id),
                            width: 170,
                            height: 170,
                            colorDark : "#000000",
                            colorLight : "#ffffff",
                            correctLevel : QRCode.CorrectLevel.H
                        })
                    }
                })
            },
            demoCustomerView(table) {
                this.showQRModal = false
                window.open(this.getQRUrl(table.id), '_blank')
            },
            async closeTableSession(table) {
                if (!confirm(`ยืนยันการปิดโต๊ะและเช็คบิลโต๊ะ ${table.name}?`)) return
                try {
                    const data = await this.apiPost('api/close_session.php', { session_id: table.sessionId })
                    if (data.status !== 'success') { alert(data.message || 'ปิดโต๊ะไม่สำเร็จ'); return }
                    await this.fetchTables()
                } catch (e) {
                    alert('เชื่อมต่อเซิร์ฟเวอร์ไม่สำเร็จ')
                }
            },
            async updateOrderStatus(orderId, newStatus) {
                try {
                    const data = await this.apiPost('api/update_order_status.php', { order_id: orderId, status: newStatus })
                    if (data.status !== 'success') { alert(data.message || 'อัปเดตสถานะไม่สำเร็จ'); return }
                    await this.fetchOrders()
                } catch (e) {
                    alert('เชื่อมต่อเซิร์ฟเวอร์ไม่สำเร็จ')
                }
            },
            async toggleStock(item) {
                const next = !item.inStock
                try {
                    const data = await this.apiPost('api/update_menu_stock.php', { menu_item_id: item.id, is_available: next })
                    if (data.status !== 'success') { alert(data.message || 'อัปเดตสต็อกไม่สำเร็จ'); return }
                    item.inStock = next
                } catch (e) {
                    alert('เชื่อมต่อเซิร์ฟเวอร์ไม่สำเร็จ')
                }
            },
            async resolveServiceCall(id) {
                try {
                    await this.apiPost('api/resolve_service_call.php', { id })
                    await this.fetchServiceCalls()
                } catch (e) {
                    alert('เชื่อมต่อเซิร์ฟเวอร์ไม่สำเร็จ')
                }
            },
            resetDemoData() {
                window.location.href = window.location.pathname
            },
            startTimer() {
                this.timer = setInterval(() => {
                    this.tables.forEach(t => {
                        if (t.status === 'occupied' && t.timeLeft > 0) {
                            t.timeLeft--
                        }
                    })
                }, 1000)
            },
            async fetchMenuItems() {
                const data = await this.apiGet('api/get_menu_items.php')
                if (data.status === 'success') {
                    this.menuItems = data.data.map(m => ({
                        id: m.id,
                        name: m.name,
                        category: m.category_name,
                        minPkg: String(m.min_package || 0),
                        emoji: m.emoji || '🍽️',
                        image: m.image_url || '',
                        inStock: !!Number(m.is_available)
                    }))
                }
            },
            async fetchTables() {
                const data = await this.apiGet('api/get_tables.php')
                if (data.status === 'success') {
                    this.tables = data.data.map(t => ({
                        id: t.id,
                        name: t.table_number,
                        status: t.status,
                        sessionId: t.session_id,
                        packageId: t.package_price ? String(t.package_price) : '',
                        packageName: t.package_name || '',
                        packagePrice: Number(t.package_price || 0),
                        adults: Number(t.adults || 0),
                        children: Number(t.children || 0),
                        timeLeft: Math.max(0, Math.round(Number(t.time_left_seconds || 0)))
                    }))
                }
            },
            async fetchOrders() {
                const data = await this.apiGet('api/get_orders.php')
                if (data.status === 'success') {
                    this.orders = data.data.map(o => ({
                        id: o.id,
                        tableId: o.table_id,
                        time: new Date(o.created_at.replace(' ', 'T')).toLocaleTimeString('th-TH', { hour: '2-digit', minute: '2-digit' }),
                        status: o.status,
                        items: o.items.map(i => ({ id: i.menu_item_id, name: i.name, qty: Number(i.quantity), note: i.note || '' }))
                    }))
                }
            },
            async fetchServiceCalls() {
                const data = await this.apiGet('api/get_service_calls.php')
                if (data.status === 'success') this.serviceCalls = data.data
            },
            async refreshAll() {
                await Promise.all([this.fetchTables(), this.fetchOrders(), this.fetchServiceCalls()])
            },
            async initStaffData() {
                await Promise.all([this.fetchMenuItems(), this.fetchTables()])
                await Promise.all([this.fetchOrders(), this.fetchServiceCalls()])
                this.startTimer()
                this.pollTimer = setInterval(() => this.refreshAll(), 5000)
            }
        },
        async mounted() {
            this.staffUnlocked = sessionStorage.getItem('staffUnlocked') === '1'
            if (!this.staffUnlocked) {
                this.showPinModal = true
                return
            }
            await this.initStaffData()
        },
        unmounted() {
            if (this.timer) clearInterval(this.timer)
            if (this.pollTimer) clearInterval(this.pollTimer)
        }
    }).mount('#app')
</script>

</body>
</html>
