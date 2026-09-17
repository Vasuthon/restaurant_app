<!DOCTYPE html>
<html lang="th">
<head>
    <?php $pageTitle = 'หลังบ้าน - พนักงาน & ครัว | HOTPOT MAN'; include __DIR__ . '/partials/head.php'; ?>
    <!-- QRCode.js Library สำหรับสร้าง QR Code จริง -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
</head>
<body class="min-h-screen antialiased flex flex-col">

<div id="app" class="flex-grow flex flex-col" v-cloak>

    <!-- TOP NAVIGATION BAR -->
    <header class="bg-white border-b border-[#E8DFCF] px-4 md:px-6 py-3 sticky top-0 z-50 flex flex-col sm:flex-row gap-3 justify-between items-center shadow-xl">
        <div class="flex items-center gap-3 w-full sm:w-auto justify-between sm:justify-start">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-accent-crimson flex items-center justify-center text-white font-bold text-xl shadow-lg">
                    <i class="fa-solid fa-pepper-hot"></i>
                </div>
                <div>
                    <h1 class="text-base md:text-lg font-bold tracking-wide text-[#1C1815] flex items-center gap-2">
                        HOTPOT MAN <span class="bg-accent-gold/20 text-accent-gold text-xs px-2 py-0.5 rounded border border-accent-gold/40 font-mono">หลังบ้าน</span>
                    </h1>
                    <p class="text-[11px] md:text-xs text-[#7A7266]">ระบบจัดการพนักงาน POS และครัว KDS</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2 md:gap-3 w-full sm:w-auto justify-end">
            <div v-if="staffUnlocked" class="bg-[#F1EBE0] p-1 rounded-xl border border-[#E8DFCF] flex items-center gap-1 w-full sm:w-auto justify-center">
                <button type="button" @click="switchMode('staff')" :class="currentMode === 'staff' ? 'bg-accent-gold text-black font-semibold shadow' : 'text-[#8F8676] hover:text-[#1C1815]'" class="flex-1 sm:flex-none px-3 md:px-4 py-1.5 rounded-lg text-xs transition-all flex items-center justify-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-user-tie"></i> พนักงาน POS
                </button>
                <button type="button" @click="switchMode('kitchen')" :class="currentMode === 'kitchen' ? 'bg-accent-crimson text-white font-semibold shadow' : 'text-[#8F8676] hover:text-[#1C1815]'" class="flex-1 sm:flex-none px-3 md:px-4 py-1.5 rounded-lg text-xs transition-all flex items-center justify-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-fire-burner"></i> ครัว KDS <span v-if="pendingOrdersCount" class="bg-red-500 text-white text-[9px] min-w-4 px-1 py-0.5 rounded-full ml-1">{{ pendingOrdersCount }}</span>
                </button>
            </div>

            <a href="index.php" class="text-xs text-[#8F8676] hover:text-[#1C1815] bg-[#F1EBE0] p-2 md:px-3 md:py-2 rounded-lg border border-[#E8DFCF] transition-colors flex items-center gap-1 cursor-pointer shrink-0">
                <i class="fa-solid fa-mobile-screen-button"></i> <span class="hidden md:inline">หน้าลูกค้า</span>
            </a>

            <button v-if="staffUnlocked" type="button" @click="staffLogout" class="text-xs text-[#7A7266] hover:text-amber-400 bg-[#F1EBE0] p-2 md:px-3 md:py-2 rounded-lg border border-[#E8DFCF] transition-colors flex items-center gap-1 cursor-pointer shrink-0">
                <i class="fa-solid fa-lock"></i> <span class="hidden md:inline">ล็อกพนักงาน</span>
            </button>

            <button type="button" @click="resetDemoData" class="text-xs text-[#7A7266] hover:text-red-400 bg-[#F1EBE0] p-2 md:px-3 md:py-2 rounded-lg border border-[#E8DFCF] transition-colors flex items-center gap-1 cursor-pointer shrink-0">
                <i class="fa-solid fa-rotate-right"></i> <span class="hidden md:inline">รีเซ็ต</span>
            </button>
        </div>
    </header>

    <!-- MAIN APP CONTAINER -->
    <main class="flex-grow p-2 sm:p-4 md:p-6 flex items-center justify-center relative">

        <!-- 🖥️ STAFF & KITCHEN INTERFACE -->
        <div v-if="staffUnlocked" class="w-full max-w-7xl min-h-[80vh] bg-[#F1EBE0] rounded-3xl border border-[#E8DFCF] shadow-2xl flex flex-col md:flex-row overflow-hidden z-10">

            <!-- SIDEBAR NAVIGATION (STAFF) -->
            <aside v-if="currentMode === 'staff'" class="w-full md:w-64 bg-white border-r border-[#E8DFCF] p-4 flex flex-col justify-between shrink-0">
                <div class="space-y-4">
                    <div class="text-xs font-bold text-[#7A7266] tracking-wider uppercase">เมนูจัดการ POS</div>
                    <nav class="space-y-1">
                        <button @click="staffTab='dashboard'" :class="staffTab==='dashboard'?'bg-accent-crimson text-white font-bold':'text-[#3D372E] hover:bg-[#EEE6D8]'" class="w-full text-left px-3 py-2.5 rounded-xl text-xs flex items-center gap-3 transition-colors">
                            <i class="fa-solid fa-chart-line text-sm"></i> แดชบอร์ดภาพรวม
                        </button>
                        <button @click="staffTab='tables'" :class="staffTab==='tables'?'bg-accent-crimson text-white font-bold':'text-[#3D372E] hover:bg-[#EEE6D8]'" class="w-full text-left px-3 py-2.5 rounded-xl text-xs flex items-center gap-3 transition-colors">
                            <i class="fa-solid fa-border-all text-sm"></i> ผังโต๊ะทั้งหมด
                        </button>
                    </nav>
                </div>

                <div class="bg-[#F1EBE0] p-3 rounded-2xl border border-[#E8DFCF] space-y-2 text-xs">
                    <div class="text-[#7A7266] font-bold">สรุปสถานะร้าน</div>
                    <div class="flex justify-between text-[#1C1815]"><span>โต๊ะว่าง:</span> <b class="text-green-400">{{ tables.filter(t => t.status==='available').length }} / {{ tables.length }}</b></div>
                    <div class="flex justify-between text-[#1C1815]"><span>มีลูกค้า:</span> <b class="text-accent-gold">{{ tables.filter(t => t.status==='occupied').length }} โต๊ะ</b></div>
                </div>
            </aside>

            <!-- SIDEBAR NAVIGATION (KITCHEN) -->
            <aside v-if="currentMode === 'kitchen'" class="w-full md:w-64 bg-white border-r border-[#E8DFCF] p-4 flex flex-col justify-between shrink-0">
                <div class="space-y-4">
                    <div class="text-xs font-bold text-[#7A7266] tracking-wider uppercase">ระบบครัว KDS</div>
                    <nav class="space-y-1">
                        <button @click="kitchenTab='orders'" :class="kitchenTab==='orders'?'bg-accent-crimson text-white font-bold':'text-[#3D372E] hover:bg-[#EEE6D8]'" class="w-full text-left px-3 py-2.5 rounded-xl text-xs flex items-center justify-between transition-colors">
                            <span class="flex items-center gap-3"><i class="fa-solid fa-fire-burner text-sm"></i> ออเดอร์เข้าครัว</span>
                            <span v-if="pendingOrdersCount" class="bg-red-500 text-white text-[9px] px-1.5 py-0.5 rounded-full font-bold">{{ pendingOrdersCount }}</span>
                        </button>
                        <button @click="kitchenTab='stock'" :class="kitchenTab==='stock'?'bg-accent-crimson text-white font-bold':'text-[#3D372E] hover:bg-[#EEE6D8]'" class="w-full text-left px-3 py-2.5 rounded-xl text-xs flex items-center gap-3 transition-colors">
                            <i class="fa-solid fa-boxes-stacked text-sm"></i> จัดการสต็อกสินค้า (วัตถุดิบ)
                        </button>
                    </nav>
                </div>
            </aside>

            <!-- MAIN CONTENT AREA -->
            <main class="flex-1 p-4 md:p-6 overflow-y-auto bg-[#F7F2EA]">

                <!-- 0. STAFF: DASHBOARD -->
                <div v-if="currentMode==='staff' && staffTab==='dashboard'" class="space-y-6">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl font-bold text-[#1C1815] flex items-center gap-2">
                            <i class="fa-solid fa-chart-line text-accent-gold"></i> แดชบอร์ดภาพรวมธุรกิจ (Business Dashboard)
                        </h2>
                        <span class="text-xs text-[#7A7266] bg-white px-3 py-1.5 rounded-xl border border-[#E8DFCF]">
                            <i class="fa-regular fa-clock mr-1"></i> อัปเดตแบบเรียลไทม์
                        </span>
                    </div>

                    <!-- ลิงก์วงแลนสำหรับลูกค้า: ตรวจ IP เครื่องอัตโนมัติ กันพนักงานต้องเปิด cmd หา ipconfig เอง
                         QR โต๊ะทุกอันอ้างอิง URL ปัจจุบันที่เปิดหน้านี้อยู่ ดังนั้นถ้าเปิดหน้านี้ผ่านลิงก์นี้ (แทน localhost)
                         มือถือลูกค้าที่ต่อไวไฟเดียวกันจะสแกน QR เข้าได้ทันที ไม่ต้องตั้งค่า router -->
                    <div v-if="lanUrl" class="bg-white border border-[#E8DFCF] rounded-2xl p-4 flex flex-wrap items-center justify-between gap-3">
                        <div class="min-w-0">
                            <div class="text-xs font-bold text-[#1C1815] flex items-center gap-1.5">
                                <i class="fa-solid fa-wifi text-accent-crimson"></i> ลิงก์สำหรับลูกค้า (ใช้ไวไฟเดียวกัน)
                            </div>
                            <div class="text-[11px] text-[#7A7266] mt-0.5">เปิดหน้านี้ผ่านลิงก์นี้แทน localhost เพื่อให้ QR โต๊ะใช้งานได้จากมือถือลูกค้า</div>
                            <div class="text-xs font-mono text-accent-crimson mt-1 truncate">{{ lanUrl }}staff.php</div>
                        </div>
                        <button @click="copyLanUrl" class="shrink-0 bg-[#F1EBE0] hover:bg-[#E8DFCF] text-[#1C1815] font-bold px-3 py-2 rounded-xl text-xs flex items-center gap-1.5">
                            <i class="fa-solid" :class="lanUrlCopied ? 'fa-check text-emerald-600' : 'fa-copy'"></i>
                            {{ lanUrlCopied ? 'คัดลอกแล้ว' : 'คัดลอกลิงก์' }}
                        </button>
                    </div>

                    <!-- Pending Service Calls (เรียกพนักงานจากลูกค้า) - เฉพาะเช็คบิล/เก็บถาด ไม่รวมเติมน้ำซุปซึ่งเป็นของครัว - อยู่บนสุดให้เห็นทันทีไม่ต้องเลื่อน -->
                    <div v-if="staffServiceCalls.length" class="bg-white border-2 border-accent-crimson rounded-2xl p-5 space-y-3">
                        <h3 class="text-sm font-bold text-[#1C1815] flex items-center gap-2">
                            <i class="fa-solid fa-bell text-accent-crimson"></i> คำเรียกพนักงานที่รอดำเนินการ
                            <span class="bg-red-500 text-white text-[10px] px-2 py-0.5 rounded-full font-bold">{{ staffServiceCalls.length }}</span>
                        </h3>
                        <div v-for="call in staffServiceCalls" :key="call.id" class="flex items-center justify-between bg-[#F1EBE0] p-3 rounded-xl border border-[#EEE6D8] text-xs">
                            <div>
                                <span class="font-bold text-[#1C1815]">โต๊ะ {{ call.table_number }}</span>
                                <span class="text-[#7A7266] ml-2">{{ call.reason }}</span>
                            </div>
                            <button @click="resolveServiceCall(call.id)" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-3 py-1.5 rounded-lg text-[10px]">
                                รับทราบ
                            </button>
                        </div>
                    </div>

                    <!-- Statistics Cards Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-white border border-[#E8DFCF] rounded-2xl p-4 flex items-center justify-between">
                            <div>
                                <div class="text-xs text-[#7A7266]">ยอดขายรวมโดยประมาณ</div>
                                <div class="text-2xl font-black text-accent-gold mt-1">{{ formatNumber(dashboardTotalRevenue) }} <small class="text-xs">฿</small></div>
                            </div>
                            <div class="w-12 h-12 rounded-2xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-amber-400 text-xl">
                                <i class="fa-solid fa-wallet"></i>
                            </div>
                        </div>

                        <div class="bg-white border border-[#E8DFCF] rounded-2xl p-4 flex items-center justify-between">
                            <div>
                                <div class="text-xs text-[#7A7266]">โต๊ะที่ใช้งานอยู่</div>
                                <div class="text-2xl font-black text-[#1C1815] mt-1">{{ tables.filter(t => t.status==='occupied').length }} <span class="text-xs text-[#8F8676]">/ {{ tables.length }} โต๊ะ</span></div>
                            </div>
                            <div class="w-12 h-12 rounded-2xl bg-red-500/10 border border-red-500/30 flex items-center justify-center text-red-400 text-xl">
                                <i class="fa-solid fa-chair"></i>
                            </div>
                        </div>

                        <div class="bg-white border border-[#E8DFCF] rounded-2xl p-4 flex items-center justify-between">
                            <div>
                                <div class="text-xs text-[#7A7266]">ออเดอร์ค้างทำ (Pending)</div>
                                <div class="text-2xl font-black text-amber-400 mt-1">{{ pendingOrdersCount }} <span class="text-xs text-[#8F8676]">รายการ</span></div>
                            </div>
                            <div class="w-12 h-12 rounded-2xl bg-yellow-500/10 border border-yellow-500/30 flex items-center justify-center text-yellow-400 text-xl">
                                <i class="fa-solid fa-clock-rotate-left"></i>
                            </div>
                        </div>

                        <div class="bg-white border border-[#E8DFCF] rounded-2xl p-4 flex items-center justify-between">
                            <div>
                                <div class="text-xs text-[#7A7266]">จำนวนถาดที่สั่งทั้งหมด</div>
                                <div class="text-2xl font-black text-emerald-400 mt-1">{{ totalTraysAllTables }} <span class="text-xs text-[#8F8676]">ถาด</span></div>
                            </div>
                            <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-400 text-xl">
                                <i class="fa-solid fa-bowl-food"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Detailed Analytics Row -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Table Occupancy Details -->
                        <div class="bg-white border border-[#E8DFCF] rounded-2xl p-5 space-y-4">
                            <h3 class="text-sm font-bold text-[#1C1815] flex items-center gap-2">
                                <i class="fa-solid fa-circle-nodes text-accent-crimson"></i> สถานะโต๊ะปัจจุบันและแพ็กเกจ
                            </h3>
                            <div class="space-y-2">
                                <div v-for="table in tables" :key="table.id" class="flex items-center justify-between bg-[#F1EBE0] p-3 rounded-xl border border-[#EEE6D8]">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs" :class="table.status==='occupied'?'bg-accent-crimson text-white':(table.status==='disabled'?'bg-[#D9CEB8] text-[#5C5446]':'bg-emerald-100 text-emerald-700')">
                                            {{ table.name }}
                                        </div>
                                        <div>
                                            <div class="text-xs font-bold text-[#1C1815]">โต๊ะ {{ table.name }}</div>
                                            <div class="text-[10px] text-[#7A7266]">{{ table.status==='occupied' ? table.packageName + ` (${table.adults} ท่าน)` : (table.status==='disabled' ? 'ปิดใช้งานชั่วคราว' : 'ว่าง') }}</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-xs font-bold" :class="table.status==='occupied'?'text-amber-500':'text-emerald-600'">
                                            {{ table.status==='occupied' ? formatNumber((table.adults * table.packagePrice) + (table.children * table.packagePrice * 0.5)) + ' ฿' : '-' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Menu Order Popularity / Quick Actions Summary -->
                        <div class="bg-white border border-[#E8DFCF] rounded-2xl p-5 space-y-4">
                            <h3 class="text-sm font-bold text-[#1C1815] flex items-center gap-2">
                                <i class="fa-solid fa-fire text-accent-gold"></i> สรุปสถานะออเดอร์ในระบบครัว
                            </h3>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center bg-[#F1EBE0] p-3 rounded-xl border border-[#EEE6D8] text-xs">
                                    <span class="text-[#3D372E]">รอดำเนินการ (Pending)</span>
                                    <span class="bg-amber-500/20 text-amber-400 font-bold px-2.5 py-1 rounded-lg border border-amber-500/30">
                                        {{ orders.filter(o => o.status === 'pending').length }} ออเดอร์
                                    </span>
                                </div>
                                <div class="flex justify-between items-center bg-[#F1EBE0] p-3 rounded-xl border border-[#EEE6D8] text-xs">
                                    <span class="text-[#3D372E]">กำลังปรุง (Cooking)</span>
                                    <span class="bg-blue-500/20 text-blue-400 font-bold px-2.5 py-1 rounded-lg border border-blue-500/30">
                                        {{ orders.filter(o => o.status === 'cooking').length }} ออเดอร์
                                    </span>
                                </div>
                                <div class="flex justify-between items-center bg-[#F1EBE0] p-3 rounded-xl border border-[#EEE6D8] text-xs">
                                    <span class="text-[#3D372E]">เสิร์ฟแล้ว (Served)</span>
                                    <span class="bg-emerald-500/20 text-emerald-400 font-bold px-2.5 py-1 rounded-lg border border-emerald-500/30">
                                        {{ orders.filter(o => o.status === 'served').length }} ออเดอร์
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 1. STAFF: TABLE MANAGEMENT -->
                <div v-if="currentMode==='staff' && staffTab==='tables'" class="space-y-6">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl font-bold text-[#1C1815] flex items-center gap-2">
                            <i class="fa-solid fa-border-all text-accent-gold"></i> ผังโต๊ะและจัดการเวลาทาน
                        </h2>
                        <button @click="openAddTableModal" class="bg-accent-crimson hover:bg-red-700 text-white font-bold px-4 py-2 rounded-xl text-xs flex items-center gap-2 cursor-pointer">
                            <i class="fa-solid fa-plus"></i> เพิ่มโต๊ะใหม่
                        </button>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div v-for="table in tables" :key="table.id" class="bg-white border border-[#E8DFCF] rounded-2xl p-4 flex flex-col justify-between space-y-3 relative overflow-hidden" :class="{'opacity-60': table.status==='disabled'}">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="text-lg font-black text-[#1C1815]">โต๊ะ {{ table.name }}</div>
                                    <div class="text-[10px]" :class="table.status==='occupied'?'text-amber-500':(table.status==='disabled'?'text-[#8F8676]':'text-green-600')">
                                        {{ table.status==='occupied' ? table.packageName : (table.status==='disabled' ? 'ปิดใช้งานชั่วคราว' : 'โต๊ะว่าง') }}
                                    </div>
                                </div>
                                <span :class="table.status==='occupied'?'bg-red-50 text-red-700 border-red-200':(table.status==='disabled'?'bg-[#EEE6D8] text-[#7A7266] border-[#D9CEB8]':'bg-emerald-50 text-emerald-700 border-emerald-200')" class="text-[9px] px-2 py-0.5 rounded-full font-bold border shrink-0">
                                    {{ table.status==='occupied'?'มีลูกค้า':(table.status==='disabled'?'ปิดอยู่':'ว่าง') }}
                                </span>
                            </div>

                            <!-- Occupied Info -->
                            <div v-if="table.status==='occupied'" class="space-y-2 text-xs bg-[#F1EBE0] p-2.5 rounded-xl border border-[#F0D8D6]">
                                <div class="flex justify-between text-[#3D372E]">
                                    <span>จำนวน:</span>
                                    <b>{{ table.adults }} ใหญ่ / {{ table.children }} เด็ก</b>
                                </div>
                                <div class="flex justify-between items-center text-[#3D372E]">
                                    <span>เวลาเหลือ:</span>
                                    <b class="text-accent-gold font-mono text-sm">{{ formatTimeLeft(table.timeLeft) }}</b>
                                </div>
                                <div class="pt-2 border-t border-[#E8DFCF] flex gap-2">
                                    <button @click="openQRModal(table)" class="flex-1 bg-accent-gold hover:bg-yellow-600 text-black font-extrabold py-1.5 rounded-lg text-[10px] flex items-center justify-center gap-1">
                                        <i class="fa-solid fa-qrcode"></i> ดู QR Code
                                    </button>
                                    <button @click="openEditTableModal(table)" class="bg-[#EEE6D8] hover:bg-[#E8DFCF] text-[#3D372E] font-bold px-2 py-1.5 rounded-lg text-[10px]">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    <button @click="openBillModal(table)" class="bg-[#EEE6D8] hover:bg-[#E8DFCF] text-[#3D372E] font-bold px-2 py-1.5 rounded-lg text-[10px]">
                                        <i class="fa-solid fa-receipt"></i> เช็คบิล
                                    </button>
                                </div>
                            </div>

                            <!-- Disabled: re-enable -->
                            <div v-else-if="table.status==='disabled'" class="pt-2 space-y-2">
                                <button @click="setTableStatus(table, 'available')" class="w-full bg-[#EEE6D8] hover:bg-[#E8DFCF] text-[#1C1815] font-bold py-2 rounded-xl text-xs flex items-center justify-center gap-1">
                                    <i class="fa-solid fa-lock-open"></i> เปิดใช้งาน
                                </button>
                                <div class="flex gap-2">
                                    <button @click="openRenameTableModal(table)" class="flex-1 bg-[#EEE6D8] hover:bg-[#E8DFCF] text-[#3D372E] font-bold py-1.5 rounded-lg text-[10px]">
                                        <i class="fa-solid fa-pen"></i> แก้ไข
                                    </button>
                                    <button @click="deleteTable(table)" class="flex-1 bg-red-50 hover:bg-red-100 text-red-700 font-bold py-1.5 rounded-lg text-[10px]">
                                        <i class="fa-solid fa-trash"></i> ลบ
                                    </button>
                                </div>
                            </div>

                            <!-- Open Table Action -->
                            <div v-else class="pt-2 space-y-2">
                                <button @click="openTableModal(table)" class="w-full bg-accent-emerald hover:bg-emerald-700 text-white font-bold py-2 rounded-xl text-xs flex items-center justify-center gap-1">
                                    <i class="fa-solid fa-plus"></i> เปิดโต๊ะใหม่
                                </button>
                                <div class="flex gap-2">
                                    <button @click="openRenameTableModal(table)" class="flex-1 bg-[#EEE6D8] hover:bg-[#E8DFCF] text-[#3D372E] font-bold py-1.5 rounded-lg text-[10px]">
                                        <i class="fa-solid fa-pen"></i> แก้ไข
                                    </button>
                                    <button @click="setTableStatus(table, 'disabled')" class="flex-1 bg-[#EEE6D8] hover:bg-[#E8DFCF] text-[#3D372E] font-bold py-1.5 rounded-lg text-[10px]">
                                        <i class="fa-solid fa-lock"></i> ปิดชั่วคราว
                                    </button>
                                    <button @click="deleteTable(table)" class="bg-red-50 hover:bg-red-100 text-red-700 font-bold px-2.5 py-1.5 rounded-lg text-[10px]">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. KITCHEN DISPLAY SYSTEM (KDS) & ORDERS -->
                <div v-if="currentMode==='kitchen' && kitchenTab==='orders'" class="space-y-6">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl font-bold text-[#1C1815] flex items-center gap-2">
                            <i class="fa-solid fa-fire-burner text-accent-crimson"></i> ออเดอร์ครัว / Kitchen Display System
                        </h2>
                    </div>

                    <!-- คำเรียกเติมน้ำซุปจากลูกค้า - เฉพาะของครัว -->
                    <div class="bg-white border border-[#E8DFCF] rounded-2xl p-5 space-y-3">
                        <h3 class="text-sm font-bold text-[#1C1815] flex items-center gap-2">
                            <i class="fa-solid fa-bowl-rice text-accent-crimson"></i> คำเรียกเติมน้ำซุป
                            <span v-if="kitchenServiceCalls.length" class="bg-red-500 text-white text-[10px] px-2 py-0.5 rounded-full font-bold">{{ kitchenServiceCalls.length }}</span>
                        </h3>
                        <div v-if="kitchenServiceCalls.length===0" class="text-center py-4 text-xs text-[#8F8676]">
                            ไม่มีคำเรียกเติมน้ำซุปในขณะนี้
                        </div>
                        <div v-for="call in kitchenServiceCalls" :key="call.id" class="flex items-center justify-between bg-[#F1EBE0] p-3 rounded-xl border border-[#EEE6D8] text-xs">
                            <div>
                                <span class="font-bold text-[#1C1815]">โต๊ะ {{ call.table_number }}</span>
                                <span class="text-[#7A7266] ml-2">{{ call.reason }}</span>
                            </div>
                            <button @click="resolveServiceCall(call.id)" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-3 py-1.5 rounded-lg text-[10px]">
                                รับทราบ
                            </button>
                        </div>
                    </div>

                    <div v-if="orders.length===0" class="text-center py-16 text-[#8F8676]">
                        <i class="fa-solid fa-circle-check text-4xl mb-2 text-emerald-500 block"></i>
                        ไม่มีออเดอร์ค้างในขณะนี้
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div v-for="ord in orders" :key="ord.id" class="bg-white border border-[#E8DFCF] rounded-2xl p-4 space-y-3">
                            <div class="flex justify-between items-center border-b border-[#E8DFCF] pb-2">
                                <div>
                                    <span class="text-lg font-black text-[#1C1815]">โต๊ะ {{ getTableName(ord.tableId) }}</span>
                                    <span class="text-[10px] text-[#7A7266] block">#{{ ord.id }} • {{ ord.time }}</span>
                                </div>
                                <span :class="getStatusBadgeClass(ord.status)" class="text-xs px-2.5 py-1 rounded-full font-bold">
                                    {{ getStatusText(ord.status) }}
                                </span>
                            </div>

                            <div class="space-y-2">
                                <div v-for="item in ord.items" :key="item.id" class="flex justify-between items-center text-xs text-[#2B2620]">
                                    <div class="min-w-0">
                                        <div class="font-bold text-[#1C1815] truncate">{{ item.name }}</div>
                                        <div v-if="item.note" class="text-[10px] text-amber-700">** {{ item.note }}</div>
                                    </div>
                                    <div class="flex items-center gap-1.5 shrink-0">
                                        <span class="bg-red-50 text-red-700 border border-red-200 font-black px-2 py-1 rounded-lg text-sm">
                                            {{ item.qty }} ถาด
                                        </span>
                                        <button @click="markOrderItemOutOfStock(item.id, item.name)" title="แจ้งของหมด" class="w-6 h-6 rounded-lg bg-[#F0F0F0] hover:bg-red-100 text-[#8F8676] hover:text-red-600 flex items-center justify-center shrink-0">
                                            <i class="fa-solid fa-ban text-[10px]"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-2 border-t border-[#E8DFCF] flex gap-2">
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
                <div v-if="currentMode==='kitchen' && kitchenTab==='stock'" class="space-y-4">
                    <div class="flex justify-between items-center flex-wrap gap-3">
                        <div>
                            <h2 class="text-xl font-bold text-[#1C1815] flex items-center gap-2">
                                <i class="fa-solid fa-boxes-stacked text-accent-gold"></i> จัดการสถานะสต็อกสินค้า (ครัว)
                            </h2>
                            <p class="text-[11px] text-[#7A7266] mt-1">{{ filteredMenuItems.length }} รายการ · กำหนดสถานะและแก้ไขวัตถุดิบได้จากจุดเดียว</p>
                        </div>
                        <div class="flex gap-2">
                            <button @click="openCategoryModal" class="bg-[#EEE6D8] hover:bg-[#E8DFCF] text-[#1C1815] font-bold px-4 py-2 rounded-xl text-xs flex items-center gap-2 cursor-pointer">
                                <i class="fa-solid fa-tags"></i> จัดการหมวดหมู่
                            </button>
                            <button @click="openAddStockModal" class="bg-accent-crimson hover:bg-red-700 text-white font-bold px-4 py-2 rounded-xl text-xs flex items-center gap-2 cursor-pointer">
                                <i class="fa-solid fa-plus"></i> เพิ่มวัตถุดิบใหม่
                            </button>
                        </div>
                    </div>

                    <div class="flex gap-2 flex-wrap sm:flex-nowrap">
                        <div class="relative flex-1 min-w-[180px]">
                            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-[#8F8676] text-xs"></i>
                            <input type="text" v-model="stockSearch" placeholder="ค้นหาชื่อหรือคำอธิบาย..." class="w-full bg-white border border-[#E8DFCF] rounded-xl pl-9 pr-3 py-2.5 text-xs text-[#1C1815] outline-none focus:border-accent-gold">
                        </div>
                        <select v-model="stockCategoryFilter" class="bg-white border border-[#E8DFCF] rounded-xl px-3 py-2.5 text-xs text-[#1C1815] outline-none shrink-0">
                            <option value="">ทุกหมวดหมู่</option>
                            <option v-for="cat in categories" :key="cat.id" :value="cat.name">{{ cat.name }}</option>
                        </select>
                    </div>

                    <div v-if="menuItems.length===0" class="text-center py-16 text-[#8F8676]">
                        <i class="fa-solid fa-box-open text-4xl mb-2 block"></i>
                        ยังไม่มีวัตถุดิบในระบบ
                    </div>
                    <div v-else-if="filteredMenuItems.length===0" class="text-center py-16 text-[#8F8676]">
                        <i class="fa-solid fa-magnifying-glass text-4xl mb-2 block"></i>
                        ไม่พบรายการที่ค้นหา
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                        <div v-for="item in filteredMenuItems" :key="item.id" class="bg-white rounded-2xl overflow-hidden shadow-lg flex flex-col">
                            <div class="relative h-24 flex items-center justify-center shrink-0" :style="{ background: categoryColor(item.category).bg }">
                                <span class="text-4xl">{{ item.emoji }}</span>
                                <span class="absolute top-2 left-2 text-[9px] font-bold px-2 py-0.5 rounded-full" :style="{ background: categoryColor(item.category).badgeBg, color: categoryColor(item.category).badgeText }">{{ item.category }}</span>
                                <span v-if="!item.inStock" class="absolute top-2 right-2 bg-red-600 text-white text-[9px] font-bold px-2 py-0.5 rounded-full">หมด</span>
                            </div>
                            <div class="p-3 flex-1 flex flex-col gap-1.5">
                                <div class="flex items-start justify-between gap-2">
                                    <span class="text-xs font-bold text-slate-800 leading-tight">{{ item.name }}</span>
                                    <span v-if="item.price > 0" class="text-xs font-bold text-accent-crimson shrink-0">฿{{ formatNumber(item.price) }}</span>
                                </div>
                                <p v-if="item.description" class="text-[10px] text-[#8F8676] leading-snug line-clamp-2">{{ item.description }}</p>
                                <span class="inline-block w-fit text-[9px] font-bold px-2 py-0.5 rounded-full" :class="item.minPkg==='0' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'">
                                    {{ item.minPkg==='0' ? 'ทุกแพ็กเกจ' : item.minPkg + '+' }}
                                </span>
                                <div class="flex items-center justify-between pt-2 mt-auto border-t border-slate-100">
                                    <label class="flex items-center gap-1.5 text-[10px] text-[#5C5446] font-semibold cursor-pointer select-none">
                                        <input type="checkbox" :checked="item.inStock" @change="toggleStock(item)" class="w-3.5 h-3.5 accent-emerald-600 cursor-pointer">
                                        พร้อมขาย
                                    </label>
                                    <div class="flex gap-1">
                                        <button @click="openEditStockModal(item)" class="w-6 h-6 rounded-lg bg-slate-100 hover:bg-slate-200 text-[#5C5446] flex items-center justify-center cursor-pointer">
                                            <i class="fa-solid fa-pen text-[10px]"></i>
                                        </button>
                                        <button @click="deleteStockItem(item)" class="w-6 h-6 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 flex items-center justify-center cursor-pointer">
                                            <i class="fa-solid fa-trash text-[10px]"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </main>
        </div>

    </main>

    <!-- MODAL 0: STAFF PIN GATE -->
    <div v-if="showPinModal" class="fixed inset-0 bg-black/80 flex items-center justify-center p-4 z-50">
        <div class="bg-white border border-[#E8DFCF] rounded-3xl p-6 max-w-sm w-full space-y-4">
            <h3 class="text-lg font-bold text-[#1C1815] flex items-center gap-2">
                <i class="fa-solid fa-lock text-accent-gold"></i> ใส่ PIN พนักงาน
            </h3>
            <p class="text-xs text-[#7A7266]">กรุณาใส่ PIN เพื่อเข้าใช้งานหน้าพนักงาน/ครัว</p>

            <input type="password" inputmode="numeric" v-model="pinInput" @keyup.enter="submitPin" placeholder="PIN" autofocus
                   class="w-full bg-[#EEE6D8] border border-[#D9CEB8] rounded-xl p-3 text-center text-lg tracking-widest text-[#1C1815] outline-none">

            <p v-if="pinError" class="text-xs text-red-400 text-center">{{ pinError }}</p>

            <div class="flex gap-2 pt-2">
                <button @click="submitPin" class="flex-1 bg-accent-crimson hover:bg-red-700 text-white font-bold py-2.5 rounded-xl text-xs">
                    ยืนยัน
                </button>
                <a href="index.php" class="bg-[#EEE6D8] text-[#3D372E] font-bold px-4 py-2.5 rounded-xl text-xs flex items-center justify-center">
                    ยกเลิก
                </a>
            </div>
        </div>
    </div>

    <!-- MODAL 1: OPEN TABLE SESSION -->
    <div v-if="showOpenModal" class="fixed inset-0 bg-black/80 flex items-center justify-center p-4 z-50">
        <div class="bg-white border border-[#E8DFCF] rounded-3xl p-6 max-w-md w-full space-y-4">
            <h3 class="text-lg font-bold text-[#1C1815] flex items-center gap-2">
                <i class="fa-solid fa-chair text-accent-gold"></i> เปิดโต๊ะ {{ selectedModalTable?.name }}
            </h3>

            <div class="space-y-3">
                <div>
                    <label class="text-xs text-[#3D372E] block mb-1">เลือกแพ็กเกจบุฟเฟต์</label>
                    <select v-model="modalPkg" class="w-full bg-[#EEE6D8] border border-[#D9CEB8] rounded-xl p-2.5 text-xs text-[#1C1815] outline-none">
                        <option value="299">Standard Mala (299 THB)</option>
                        <option value="399">Premium Pork & Beef (399 THB)</option>
                        <option value="499">Seafood & Wagyu Supreme (499 THB)</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs text-[#3D372E] block mb-1">ผู้ใหญ่ (คน)</label>
                        <input type="number" min="1" v-model.number="modalAdults" class="w-full bg-[#EEE6D8] border border-[#D9CEB8] rounded-xl p-2.5 text-xs text-[#1C1815]">
                    </div>
                    <div>
                        <label class="text-xs text-[#3D372E] block mb-1">เด็ก (คน)</label>
                        <input type="number" min="0" v-model.number="modalChildren" class="w-full bg-[#EEE6D8] border border-[#D9CEB8] rounded-xl p-2.5 text-xs text-[#1C1815]">
                    </div>
                </div>

                <div>
                    <label class="text-xs text-[#3D372E] block mb-1">เลือกน้ำซุป (ไม่บังคับ สั่งเข้าครัวทันที)</label>
                    <select v-model="modalSoup" class="w-full bg-[#EEE6D8] border border-[#D9CEB8] rounded-xl p-2.5 text-xs text-[#1C1815] outline-none">
                        <option value="">ให้ลูกค้าเลือกเอง</option>
                        <option v-for="soup in soupMenuItems" :key="soup.id" :value="soup.id">{{ soup.emoji }} {{ soup.name }}{{ soup.price > 0 ? ' (+' + formatNumber(soup.price) + '฿)' : '' }}</option>
                    </select>
                </div>
            </div>

            <div class="flex gap-2 pt-2">
                <button @click="confirmOpenTable" class="flex-1 bg-accent-crimson hover:bg-red-700 text-white font-bold py-2.5 rounded-xl text-xs">
                    ยืนยันเปิดโต๊ะ
                </button>
                <button @click="showOpenModal=false" class="bg-[#EEE6D8] text-[#3D372E] font-bold px-4 py-2.5 rounded-xl text-xs">
                    ยกเลิก
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL 1B: EDIT OPEN TABLE (จำนวนคน / ต่อเวลา) -->
    <div v-if="showEditTableModal" class="fixed inset-0 bg-black/80 flex items-center justify-center p-4 z-50">
        <div class="bg-white border border-[#E8DFCF] rounded-3xl p-6 max-w-md w-full space-y-4">
            <h3 class="text-lg font-bold text-[#1C1815] flex items-center gap-2">
                <i class="fa-solid fa-pen text-accent-gold"></i> แก้ไขโต๊ะ {{ editTableTarget?.name }}
            </h3>

            <div class="space-y-3">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs text-[#3D372E] block mb-1">ผู้ใหญ่ (คน)</label>
                        <input type="number" min="1" v-model.number="editAdults" class="w-full bg-[#EEE6D8] border border-[#D9CEB8] rounded-xl p-2.5 text-xs text-[#1C1815]">
                    </div>
                    <div>
                        <label class="text-xs text-[#3D372E] block mb-1">เด็ก (คน)</label>
                        <input type="number" min="0" v-model.number="editChildren" class="w-full bg-[#EEE6D8] border border-[#D9CEB8] rounded-xl p-2.5 text-xs text-[#1C1815]">
                    </div>
                </div>
            </div>

            <p v-if="editTableError" class="text-xs text-red-500 text-center">{{ editTableError }}</p>

            <div class="flex gap-2 pt-2">
                <button @click="saveEditTable" class="flex-1 bg-accent-crimson hover:bg-red-700 text-white font-bold py-2.5 rounded-xl text-xs">
                    บันทึกจำนวนคน
                </button>
                <button @click="showEditTableModal=false" class="bg-[#EEE6D8] text-[#3D372E] font-bold px-4 py-2.5 rounded-xl text-xs">
                    ปิด
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL 1C: ADD / RENAME TABLE -->
    <div v-if="showTableFormModal" class="fixed inset-0 bg-black/80 flex items-center justify-center p-4 z-50">
        <div class="bg-white border border-[#E8DFCF] rounded-3xl p-6 max-w-sm w-full space-y-4">
            <h3 class="text-lg font-bold text-[#1C1815] flex items-center gap-2">
                <i class="fa-solid fa-border-all text-accent-gold"></i> {{ tableFormMode === 'add' ? 'เพิ่มโต๊ะใหม่' : 'แก้ไขชื่อโต๊ะ' }}
            </h3>

            <div>
                <label class="text-xs text-[#3D372E] block mb-1">ชื่อ/เลขโต๊ะ</label>
                <input type="text" v-model="tableFormNumber" placeholder="เช่น A11, VIP1" class="w-full bg-[#EEE6D8] border border-[#D9CEB8] rounded-xl p-2.5 text-xs text-[#1C1815]" @keyup.enter="saveTableForm">
            </div>

            <p v-if="tableFormError" class="text-xs text-red-500 text-center">{{ tableFormError }}</p>

            <div class="flex gap-2 pt-2">
                <button @click="saveTableForm" class="flex-1 bg-accent-crimson hover:bg-red-700 text-white font-bold py-2.5 rounded-xl text-xs">
                    {{ tableFormMode === 'add' ? 'เพิ่มโต๊ะ' : 'บันทึก' }}
                </button>
                <button @click="showTableFormModal=false" class="bg-[#EEE6D8] text-[#3D372E] font-bold px-4 py-2.5 rounded-xl text-xs">
                    ยกเลิก
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL 2: DISPLAY QR CODE FOR CUSTOMER PHONE SCANNING -->
    <div v-if="showQRModal" class="fixed inset-0 bg-black/80 flex items-center justify-center p-4 z-50">
        <div class="bg-white border border-[#E8DFCF] rounded-3xl p-6 max-w-sm w-full space-y-4 text-center">
            <h3 class="text-lg font-bold text-[#1C1815] flex items-center justify-center gap-2">
                <i class="fa-solid fa-qrcode text-accent-gold"></i> QR Code โต๊ะ {{ selectedQRTable?.name }}
            </h3>
            <p class="text-xs text-[#3D372E]">
                แพ็กเกจ: <b class="text-amber-400">{{ selectedQRTable?.packageName }}</b>
            </p>

            <!-- QR Container generated dynamically -->
            <div class="bg-white p-4 rounded-2xl inline-block shadow-lg my-2">
                <div id="qrcode-container" class="flex justify-center"></div>
            </div>

            <div class="text-[11px] text-[#3D372E] bg-[#F1EBE0] p-2.5 rounded-xl border border-[#E8DFCF] break-all font-mono">
                {{ getQRUrl(selectedQRTable?.id) }}
            </div>

            <p class="text-[11px] text-[#7A7266] leading-relaxed">
                📱 ใช้กล้องมือถือสแกนเพื่อสั่งอาหาร<br>
                <span class="text-amber-400 text-[10px]">*ต้องเชื่อมต่อ Wi-Fi เดียวกันกับเครื่อง Server</span>
            </p>

            <div class="flex flex-col gap-2 pt-2">
                <button @click="demoCustomerView(selectedQRTable)" class="w-full bg-accent-crimson hover:bg-red-700 text-white font-bold py-2.5 rounded-xl text-xs flex items-center justify-center gap-2">
                    <i class="fa-solid fa-mobile-screen"></i> เปิดดูหน้ามือถือลูกค้า (แท็บใหม่)
                </button>
                <button @click="showQRModal=false" class="w-full bg-[#EEE6D8] text-[#3D372E] font-bold py-2.5 rounded-xl text-xs">
                    ปิดหน้าต่าง
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL 3: ADD/EDIT STOCK ITEM -->
    <div v-if="showStockModal" class="fixed inset-0 bg-black/80 flex items-center justify-center p-4 z-50">
        <div class="bg-white border border-[#E8DFCF] rounded-3xl p-6 max-w-md w-full space-y-4 max-h-[90vh] overflow-y-auto">
            <h3 class="text-lg font-bold text-[#1C1815] flex items-center gap-2">
                <i class="fa-solid fa-boxes-stacked text-accent-gold"></i> {{ stockModalMode === 'add' ? 'เพิ่มวัตถุดิบใหม่' : 'แก้ไขวัตถุดิบ' }}
            </h3>

            <div class="space-y-3">
                <div>
                    <label class="text-xs text-[#3D372E] block mb-1">ชื่อวัตถุดิบ *</label>
                    <input type="text" v-model="stockForm.name" placeholder="เช่น กุ้งแม่น้ำสด" class="w-full bg-[#EEE6D8] border border-[#D9CEB8] rounded-xl p-2.5 text-xs text-[#1C1815] outline-none">
                </div>

                <div>
                    <label class="text-xs text-[#3D372E] block mb-1">หมวดหมู่ *</label>
                    <select v-model="stockForm.category_id" class="w-full bg-[#EEE6D8] border border-[#D9CEB8] rounded-xl p-2.5 text-xs text-[#1C1815] outline-none">
                        <option value="" disabled>เลือกหมวดหมู่</option>
                        <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs text-[#3D372E] block mb-1">อิโมจิ</label>
                        <input type="text" v-model="stockForm.emoji" placeholder="🦐" maxlength="10" class="w-full bg-[#EEE6D8] border border-[#D9CEB8] rounded-xl p-2.5 text-xs text-[#1C1815] outline-none">
                    </div>
                    <div>
                        <label class="text-xs text-[#3D372E] block mb-1">แพ็กเกจขั้นต่ำ</label>
                        <select v-model.number="stockForm.min_package" class="w-full bg-[#EEE6D8] border border-[#D9CEB8] rounded-xl p-2.5 text-xs text-[#1C1815] outline-none">
                            <option :value="0">ทุกแพ็กเกจ</option>
                            <option :value="299">299+</option>
                            <option :value="399">399+</option>
                            <option :value="499">499+</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="text-xs text-[#3D372E] block mb-1">ราคาเพิ่มเติม (บาท ถ้ามี)</label>
                    <input type="number" min="0" step="0.01" v-model.number="stockForm.price" class="w-full bg-[#EEE6D8] border border-[#D9CEB8] rounded-xl p-2.5 text-xs text-[#1C1815] outline-none">
                </div>

                <div>
                    <label class="text-xs text-[#3D372E] block mb-1">รูปภาพ</label>
                    <div class="flex items-center gap-3">
                        <div class="w-16 h-16 rounded-xl bg-[#EEE6D8] border border-[#D9CEB8] overflow-hidden flex items-center justify-center shrink-0">
                            <img v-if="stockForm.image_url" :src="stockForm.image_url" class="w-full h-full object-cover" @error="$event.target.style.visibility='hidden'">
                            <i v-else class="fa-solid fa-image text-[#5C5446]"></i>
                        </div>
                        <div class="flex-1 space-y-1.5">
                            <input type="file" accept="image/*" @change="handleImageUpload" class="hidden" id="stockImageFile">
                            <label for="stockImageFile" class="w-full block text-center bg-[#EEE6D8] hover:bg-[#E8DFCF] text-[#2B2620] font-bold py-2 rounded-lg text-[10px] cursor-pointer border border-[#D9CEB8] transition-colors">
                                <i class="fa-solid" :class="uploadingImage ? 'fa-spinner fa-spin' : 'fa-upload'"></i> {{ uploadingImage ? 'กำลังอัปโหลด...' : 'อัปโหลดรูปจากเครื่อง' }}
                            </label>
                            <input type="text" v-model="stockForm.image_url" placeholder="หรือวาง URL รูปภาพ" class="w-full bg-[#EEE6D8] border border-[#D9CEB8] rounded-lg p-1.5 text-[10px] text-[#1C1815] outline-none">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="text-xs text-[#3D372E] block mb-1">คำอธิบาย</label>
                    <textarea v-model="stockForm.description" rows="2" class="w-full bg-[#EEE6D8] border border-[#D9CEB8] rounded-xl p-2.5 text-xs text-[#1C1815] outline-none resize-none"></textarea>
                </div>

                <label class="flex items-center gap-2 text-xs text-[#3D372E] cursor-pointer">
                    <input type="checkbox" v-model="stockForm.is_available" class="w-4 h-4 accent-accent-emerald">
                    มีสินค้าพร้อมขาย
                </label>
            </div>

            <p v-if="stockFormError" class="text-xs text-red-400 text-center">{{ stockFormError }}</p>

            <div class="flex gap-2 pt-2">
                <button @click="saveStockItem" class="flex-1 bg-accent-crimson hover:bg-red-700 text-white font-bold py-2.5 rounded-xl text-xs">
                    {{ stockModalMode === 'add' ? 'เพิ่มวัตถุดิบ' : 'บันทึกการแก้ไข' }}
                </button>
                <button @click="showStockModal=false" class="bg-[#EEE6D8] text-[#3D372E] font-bold px-4 py-2.5 rounded-xl text-xs">
                    ยกเลิก
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL 4: MANAGE CATEGORIES -->
    <div v-if="showCategoryModal" class="fixed inset-0 bg-black/80 flex items-center justify-center p-4 z-50">
        <div class="bg-white border border-[#E8DFCF] rounded-3xl p-6 max-w-md w-full space-y-4 max-h-[90vh] overflow-y-auto">
            <h3 class="text-lg font-bold text-[#1C1815] flex items-center gap-2">
                <i class="fa-solid fa-tags text-accent-gold"></i> จัดการหมวดหมู่วัตถุดิบ
            </h3>

            <div class="space-y-3 bg-[#F1EBE0] p-3 rounded-xl border border-[#EEE6D8]">
                <div>
                    <label class="text-xs text-[#3D372E] block mb-1">ชื่อหมวดหมู่ *</label>
                    <input type="text" v-model="categoryForm.name" placeholder="เช่น เครื่องดื่ม" class="w-full bg-[#EEE6D8] border border-[#D9CEB8] rounded-xl p-2.5 text-xs text-[#1C1815] outline-none">
                </div>
                <div>
                    <label class="text-xs text-[#3D372E] block mb-1">คำอธิบาย</label>
                    <input type="text" v-model="categoryForm.description" class="w-full bg-[#EEE6D8] border border-[#D9CEB8] rounded-xl p-2.5 text-xs text-[#1C1815] outline-none">
                </div>
                <p v-if="categoryFormError" class="text-xs text-red-400">{{ categoryFormError }}</p>
                <div class="flex gap-2">
                    <button @click="saveCategory" class="flex-1 bg-accent-crimson hover:bg-red-700 text-white font-bold py-2 rounded-xl text-xs">
                        {{ categoryEditId ? 'บันทึกการแก้ไข' : 'เพิ่มหมวดหมู่' }}
                    </button>
                    <button v-if="categoryEditId" @click="resetCategoryForm" class="bg-[#EEE6D8] text-[#3D372E] font-bold px-3 py-2 rounded-xl text-xs">
                        ยกเลิก
                    </button>
                </div>
            </div>

            <div class="space-y-2">
                <div v-for="cat in categories" :key="cat.id" class="flex items-center justify-between bg-[#F1EBE0] p-3 rounded-xl border border-[#EEE6D8]">
                    <div class="min-w-0">
                        <div class="text-xs font-bold text-[#1C1815] truncate">{{ cat.name }}</div>
                        <div class="text-[10px] text-[#7A7266] truncate">{{ cat.description }}</div>
                    </div>
                    <div class="flex gap-1.5 shrink-0">
                        <button @click="editCategory(cat)" class="bg-[#EEE6D8] hover:bg-[#E8DFCF] text-[#2B2620] font-bold px-2.5 py-1.5 rounded-lg text-[10px] cursor-pointer">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <button @click="deleteCategory(cat)" class="bg-red-900/60 hover:bg-red-800 text-red-200 font-bold px-2.5 py-1.5 rounded-lg text-[10px] cursor-pointer">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>

            <button @click="showCategoryModal=false" class="w-full bg-[#EEE6D8] text-[#3D372E] font-bold py-2.5 rounded-xl text-xs">
                ปิดหน้าต่าง
            </button>
        </div>
    </div>

    <!-- MODAL 5: CHECK BILL -->
    <div v-if="showBillModal" class="fixed inset-0 bg-black/80 flex items-center justify-center p-4 z-50">
        <div class="bg-white border border-[#E8DFCF] rounded-3xl p-6 max-w-md w-full space-y-4 max-h-[90vh] overflow-y-auto">
            <h3 class="text-lg font-bold text-[#1C1815] flex items-center gap-2">
                <i class="fa-solid fa-receipt text-accent-gold"></i> บิลโต๊ะ {{ billTable?.name }}
            </h3>

            <div v-if="billLoading" class="text-center py-10 text-[#7A7266] text-xs">
                <i class="fa-solid fa-spinner fa-spin mr-1"></i> กำลังคำนวณบิล...
            </div>

            <div v-else-if="billData" class="space-y-3 text-xs">
                <div class="bg-[#F1EBE0] p-3 rounded-xl border border-[#EEE6D8] space-y-1.5">
                    <div class="flex justify-between text-[#3D372E]">
                        <span>{{ billData.package_name }} ({{ formatNumber(billData.package_price) }} ฿/ท่าน)</span>
                    </div>
                    <div class="flex justify-between text-[#7A7266] text-[10px]">
                        <span>ผู้ใหญ่ {{ billData.adults }} × {{ formatNumber(billData.package_price) }}, เด็ก {{ billData.children }} × {{ formatNumber(billData.package_price * 0.5) }}</span>
                        <span class="text-[#1C1815] font-bold">{{ formatNumber(billData.buffet_charge) }} ฿</span>
                    </div>
                </div>

                <div v-if="billData.extra_items.length" class="bg-[#F1EBE0] p-3 rounded-xl border border-[#EEE6D8] space-y-1.5">
                    <div class="text-[10px] text-[#7A7266] font-bold uppercase tracking-wide mb-1">รายการเสริม</div>
                    <div v-for="item in billData.extra_items" :key="item.name" class="flex justify-between text-[#3D372E]">
                        <span>{{ item.name }} × {{ item.qty }}</span>
                        <span class="text-[#1C1815]">{{ formatNumber(item.line_total) }} ฿</span>
                    </div>
                </div>

                <div v-if="billData.solo_soup_charge > 0" class="bg-amber-50 p-3 rounded-xl border border-amber-300 space-y-1">
                    <div class="flex justify-between text-amber-800 font-bold">
                        <span>ค่าน้ำซุป (ทานคนเดียว)</span>
                        <span>{{ formatNumber(billData.solo_soup_charge) }} ฿</span>
                    </div>
                    <div class="text-[10px] text-amber-700">เหมาจ่ายขั้นต่ำ 99 บาท/โต๊ะ เมื่อทานคนเดียวและสั่งน้ำซุปฟรี</div>
                </div>

                <div class="pt-2 border-t border-[#E8DFCF] space-y-1.5">
                    <div class="flex justify-between text-[#3D372E]">
                        <span>ยอดรวม</span>
                        <span>{{ formatNumber(billData.subtotal) }} ฿</span>
                    </div>
                    <div class="flex justify-between text-[#7A7266]">
                        <span>VAT 7%</span>
                        <span>{{ formatNumber(billData.vat) }} ฿</span>
                    </div>
                    <div class="flex justify-between text-[#1C1815] font-black text-base pt-1.5 border-t border-[#E8DFCF]">
                        <span>ยอดสุทธิ</span>
                        <span class="text-accent-gold">{{ formatNumber(billData.grand_total) }} ฿</span>
                    </div>
                </div>
            </div>

            <div class="flex gap-2 pt-2">
                <button v-if="billData" @click="printReceipt" class="bg-[#EEE6D8] hover:bg-[#E8DFCF] text-[#3D372E] font-bold px-3 py-2.5 rounded-xl text-xs shrink-0">
                    <i class="fa-solid fa-print"></i>
                </button>
                <button @click="confirmCloseTable" class="flex-1 bg-accent-crimson hover:bg-red-700 text-white font-bold py-2.5 rounded-xl text-xs">
                    ยืนยันปิดโต๊ะ
                </button>
                <button @click="showBillModal=false" class="bg-[#EEE6D8] text-[#3D372E] font-bold px-4 py-2.5 rounded-xl text-xs">
                    ปิดหน้าต่าง
                </button>
            </div>
        </div>
    </div>

    <!-- PRINT-ONLY RECEIPT (invisible on screen, shown via @media print) -->
    <div id="receiptPrintArea" v-if="billData">
        <div style="font-family: 'Courier New', monospace; width: 280px; margin: 0 auto; color: #000; padding: 16px 0;">
            <div style="text-align:center;">
                <div style="font-size:16px; font-weight:bold; letter-spacing:1px;">HOTPOT MAN</div>
                <div style="font-size:10px; margin-top:2px;">ใบเสร็จรับเงิน / RECEIPT</div>
                <div style="font-size:10px; margin-top:6px;">โต๊ะ {{ billTable?.name }}</div>
                <div style="font-size:9px; color:#333;">{{ receiptDateTime }}</div>
            </div>

            <div style="border-top:1px dashed #000; margin:10px 0;"></div>

            <div style="font-size:11px; line-height:1.6;">
                <div style="display:flex; justify-content:space-between;">
                    <span>{{ billData.package_name }}</span>
                </div>
                <div style="display:flex; justify-content:space-between;">
                    <span>&nbsp;&nbsp;ผู้ใหญ่ {{ billData.adults }} x {{ formatNumber(billData.package_price) }}</span>
                    <span>{{ formatNumber(billData.adults * billData.package_price) }}</span>
                </div>
                <div v-if="billData.children > 0" style="display:flex; justify-content:space-between;">
                    <span>&nbsp;&nbsp;เด็ก {{ billData.children }} x {{ formatNumber(billData.package_price * 0.5) }}</span>
                    <span>{{ formatNumber(billData.children * billData.package_price * 0.5) }}</span>
                </div>

                <template v-if="billData.extra_items.length">
                    <div style="border-top:1px dashed #000; margin:8px 0;"></div>
                    <div v-for="item in billData.extra_items" :key="item.name" style="display:flex; justify-content:space-between;">
                        <span>{{ item.name }} x{{ item.qty }}</span>
                        <span>{{ formatNumber(item.line_total) }}</span>
                    </div>
                </template>

                <template v-if="billData.solo_soup_charge > 0">
                    <div style="display:flex; justify-content:space-between;">
                        <span>ค่าน้ำซุป (ทานคนเดียว)</span>
                        <span>{{ formatNumber(billData.solo_soup_charge) }}</span>
                    </div>
                </template>

                <div style="border-top:1px dashed #000; margin:8px 0;"></div>
                <div style="display:flex; justify-content:space-between;">
                    <span>ยอดรวม</span>
                    <span>{{ formatNumber(billData.subtotal) }}</span>
                </div>
                <div style="display:flex; justify-content:space-between;">
                    <span>VAT 7%</span>
                    <span>{{ formatNumber(billData.vat) }}</span>
                </div>
                <div style="border-top:1px solid #000; margin:6px 0; padding-top:6px; display:flex; justify-content:space-between; font-weight:bold; font-size:14px;">
                    <span>ยอดสุทธิ</span>
                    <span>{{ formatNumber(billData.grand_total) }} ฿</span>
                </div>
            </div>

            <div style="border-top:1px dashed #000; margin:10px 0;"></div>
            <div style="text-align:center;">
                <div id="receiptQRCode" style="display:inline-block;"></div>
                <div style="font-size:9px; margin-top:4px; color:#333;">QR ตัวอย่างสำหรับชำระเงิน (ไม่ใช่ช่องทางจริง)</div>
            </div>
            <div style="border-top:1px dashed #000; margin:10px 0;"></div>
            <div style="text-align:center; font-size:10px;">ขอบคุณที่ใช้บริการ</div>
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
                modalSoup: '',

                showEditTableModal: false,
                editTableTarget: null,
                editAdults: 1,
                editChildren: 0,
                editTableError: '',

                showTableFormModal: false,
                tableFormMode: 'add',
                tableFormNumber: '',
                tableFormTarget: null,
                tableFormError: '',

                showQRModal: false,
                selectedQRTable: null,

                stockSearch: '',
                stockCategoryFilter: '',

                showStockModal: false,
                stockModalMode: 'add',
                stockForm: { id: null, category_id: '', name: '', description: '', price: 0, image_url: '', emoji: '', min_package: 0, is_available: true },
                stockFormError: '',
                uploadingImage: false,

                showCategoryModal: false,
                categoryForm: { name: '', description: '' },
                categoryEditId: null,
                categoryFormError: '',

                showBillModal: false,
                billTable: null,
                billData: null,
                billLoading: false,

                menuItems: [],
                categories: [],
                tables: [],
                serviceCalls: [],
                orders: [],

                staffUnlocked: false,
                showPinModal: false,
                pinInput: '',
                pinError: '',

                timer: null,
                pollTimer: null,
                knownPendingOrderIds: null,
                knownServiceCallIds: null,

                lanUrl: '',
                lanUrlCopied: false
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
            },
            filteredMenuItems() {
                const q = this.stockSearch.trim().toLowerCase()
                return this.menuItems.filter(item => {
                    const matchCat = !this.stockCategoryFilter || item.category === this.stockCategoryFilter
                    const matchSearch = !q || item.name.toLowerCase().includes(q) || (item.description && item.description.toLowerCase().includes(q))
                    return matchCat && matchSearch
                })
            },
            receiptDateTime() {
                return new Date().toLocaleString('th-TH', { dateStyle: 'medium', timeStyle: 'short' })
            },
            kitchenServiceCalls() {
                return this.serviceCalls.filter(c => c.reason.includes('น้ำซุป'))
            },
            soupMenuItems() {
                return this.menuItems.filter(m => m.category === 'น้ำซุป')
            },
            staffServiceCalls() {
                return this.serviceCalls.filter(c => !c.reason.includes('น้ำซุป'))
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
                this.modalSoup = ''
                this.showOpenModal = true
            },
            openAddTableModal() {
                this.tableFormMode = 'add'
                this.tableFormNumber = ''
                this.tableFormTarget = null
                this.tableFormError = ''
                this.showTableFormModal = true
            },
            openRenameTableModal(table) {
                this.tableFormMode = 'rename'
                this.tableFormNumber = table.name
                this.tableFormTarget = table
                this.tableFormError = ''
                this.showTableFormModal = true
            },
            async saveTableForm() {
                if (!this.tableFormNumber.trim()) {
                    this.tableFormError = 'กรุณากรอกชื่อ/เลขโต๊ะ'
                    return
                }
                try {
                    let data
                    if (this.tableFormMode === 'add') {
                        data = await this.apiPost('api/add_table.php', { table_number: this.tableFormNumber.trim() })
                    } else {
                        data = await this.apiPost('api/update_table.php', { id: this.tableFormTarget.id, table_number: this.tableFormNumber.trim() })
                    }
                    if (data.status !== 'success') { this.tableFormError = data.message || 'บันทึกไม่สำเร็จ'; return }
                    this.showTableFormModal = false
                    await this.fetchTables()
                } catch (e) {
                    this.tableFormError = 'เชื่อมต่อเซิร์ฟเวอร์ไม่สำเร็จ'
                }
            },
            async setTableStatus(table, status) {
                const msg = status === 'disabled' ? `ยืนยันปิดใช้งานโต๊ะ ${table.name} ชั่วคราว?` : `ยืนยันเปิดใช้งานโต๊ะ ${table.name}?`
                if (!confirm(msg)) return
                try {
                    const data = await this.apiPost('api/update_table.php', { id: table.id, status })
                    if (data.status !== 'success') { alert(data.message || 'แก้ไขไม่สำเร็จ'); return }
                    await this.fetchTables()
                } catch (e) {
                    alert('เชื่อมต่อเซิร์ฟเวอร์ไม่สำเร็จ')
                }
            },
            async deleteTable(table) {
                if (!confirm(`ยืนยันลบโต๊ะ ${table.name}?`)) return
                try {
                    const data = await this.apiPost('api/delete_table.php', { id: table.id })
                    if (data.status !== 'success') { alert(data.message || 'ลบไม่สำเร็จ'); return }
                    await this.fetchTables()
                } catch (e) {
                    alert('เชื่อมต่อเซิร์ฟเวอร์ไม่สำเร็จ')
                }
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

                    if (this.modalSoup && data.session_id) {
                        await this.apiPost('api/create_order.php', {
                            session_id: data.session_id,
                            items: [{ menu_item_id: this.modalSoup, quantity: 1 }]
                        })
                        await this.fetchOrders()
                    }

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
            openEditTableModal(table) {
                this.editTableTarget = table
                this.editAdults = table.adults
                this.editChildren = table.children
                this.editTableError = ''
                this.showEditTableModal = true
            },
            async saveEditTable() {
                if (!this.editTableTarget) return
                try {
                    const data = await this.apiPost('api/update_session.php', {
                        session_id: this.editTableTarget.sessionId,
                        adults: this.editAdults,
                        children: this.editChildren
                    })
                    if (data.status !== 'success') { this.editTableError = data.message || 'แก้ไขไม่สำเร็จ'; return }
                    this.showEditTableModal = false
                    await this.fetchTables()
                } catch (e) {
                    this.editTableError = 'เชื่อมต่อเซิร์ฟเวอร์ไม่สำเร็จ'
                }
            },
            async openBillModal(table) {
                this.billTable = table
                this.showBillModal = true
                this.billLoading = true
                this.billData = null
                try {
                    const data = await this.apiGet('api/get_bill.php?session_id=' + encodeURIComponent(table.sessionId))
                    if (data.status !== 'success') { alert(data.message || 'ดึงข้อมูลบิลไม่สำเร็จ'); this.showBillModal = false; return }
                    this.billData = data.data
                    this.$nextTick(() => this.renderReceiptQRCode())
                } catch (e) {
                    alert('เชื่อมต่อเซิร์ฟเวอร์ไม่สำเร็จ')
                    this.showBillModal = false
                } finally {
                    this.billLoading = false
                }
            },
            renderReceiptQRCode() {
                const container = document.getElementById('receiptQRCode')
                if (!container || !this.billData || !this.billTable) return
                container.innerHTML = ''
                new QRCode(container, {
                    text: `DEMO-RECEIPT | โต๊ะ ${this.billTable.name} | ยอด ${this.formatNumber(this.billData.grand_total)} บาท | ตัวอย่างเท่านั้น ไม่ใช่ช่องทางชำระเงินจริง`,
                    width: 110,
                    height: 110,
                    colorDark: '#000000',
                    colorLight: '#ffffff',
                    correctLevel: QRCode.CorrectLevel.M
                })
            },
            printReceipt() {
                window.print()
            },
            async confirmCloseTable() {
                if (!this.billTable) return
                if (!confirm(`ยืนยันการปิดโต๊ะ ${this.billTable.name}?`)) return
                try {
                    const data = await this.apiPost('api/close_session.php', { session_id: this.billTable.sessionId })
                    if (data.status !== 'success') { alert(data.message || 'ปิดโต๊ะไม่สำเร็จ'); return }
                    this.showBillModal = false
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
            async markOrderItemOutOfStock(menuItemId, itemName) {
                if (!confirm(`ยืนยันปรับ "${itemName}" เป็นหมด? ระบบจะแจ้งเตือนทุกโต๊ะที่สั่งรายการนี้ค้างอยู่`)) return
                try {
                    const data = await this.apiPost('api/mark_order_item_unavailable.php', { menu_item_id: menuItemId })
                    if (data.status !== 'success') { alert(data.message || 'แจ้งของหมดไม่สำเร็จ'); return }
                    const item = this.menuItems.find(m => m.id === menuItemId)
                    if (item) item.inStock = false
                    await this.fetchOrders()
                    alert(`แจ้งของหมด "${itemName}" เรียบร้อยแล้ว (แจ้งเตือนไปแล้ว ${data.affected_items || 0} รายการที่ยังค้างอยู่)`)
                } catch (e) {
                    alert('เชื่อมต่อเซิร์ฟเวอร์ไม่สำเร็จ')
                }
            },
            async toggleStock(item) {
                const next = !item.inStock
                const confirmMsg = next
                    ? `ยืนยันปรับ "${item.name}" กลับมาพร้อมขาย?`
                    : `ยืนยันปรับ "${item.name}" เป็นหมด/งดขายชั่วคราว?`
                if (!confirm(confirmMsg)) return
                try {
                    const data = await this.apiPost('api/update_menu_stock.php', { menu_item_id: item.id, is_available: next })
                    if (data.status !== 'success') { alert(data.message || 'อัปเดตสต็อกไม่สำเร็จ'); return }
                    item.inStock = next
                } catch (e) {
                    alert('เชื่อมต่อเซิร์ฟเวอร์ไม่สำเร็จ')
                }
            },
            categoryColor(categoryName) {
                const palette = {
                    'เนื้อสัตว์': { bg: 'linear-gradient(135deg,#FCE4E4,#FBD3D3)', badgeBg: '#FEE2E2', badgeText: '#B91C1C' },
                    'ซีฟู้ด': { bg: 'linear-gradient(135deg,#E0F2FE,#CFEAFB)', badgeBg: '#DBEAFE', badgeText: '#1D4ED8' },
                    'ผักและลูกชิ้น': { bg: 'linear-gradient(135deg,#DCFCE7,#CDF7DE)', badgeBg: '#DCFCE7', badgeText: '#15803D' },
                    'น้ำซุป': { bg: 'linear-gradient(135deg,#FFEDD5,#FEE1BE)', badgeBg: '#FFEDD5', badgeText: '#C2410C' },
                    'น้ำจิ้ม': { bg: 'linear-gradient(135deg,#FEF9C3,#FDF3AD)', badgeBg: '#FEF9C3', badgeText: '#854D0E' },
                    'เครื่องดื่ม': { bg: 'linear-gradient(135deg,#CFFAFE,#B8F3FA)', badgeBg: '#CFFAFE', badgeText: '#0E7490' },
                    'ไอศกรีม': { bg: 'linear-gradient(135deg,#FCE7F3,#FAD7EA)', badgeBg: '#FCE7F3', badgeText: '#BE185D' },
                    'ของทานเล่น': { bg: 'linear-gradient(135deg,#FEF3C7,#FDECA8)', badgeBg: '#FEF3C7', badgeText: '#92400E' },
                    'ของหวาน': { bg: 'linear-gradient(135deg,#FFE4E6,#FFD3D8)', badgeBg: '#FFE4E6', badgeText: '#9F1239' },
                }
                return palette[categoryName] || { bg: 'linear-gradient(135deg,#F1F5F9,#E2E8F0)', badgeBg: '#F1F5F9', badgeText: '#475569' }
            },
            openAddStockModal() {
                this.stockModalMode = 'add'
                this.stockForm = { id: null, category_id: this.categories[0]?.id || '', name: '', description: '', price: 0, image_url: '', emoji: '', min_package: 0, is_available: true }
                this.stockFormError = ''
                this.showStockModal = true
            },
            openEditStockModal(item) {
                this.stockModalMode = 'edit'
                this.stockForm = {
                    id: item.id,
                    category_id: item.categoryId,
                    name: item.name,
                    description: item.description,
                    price: item.price,
                    image_url: item.image,
                    emoji: item.emoji,
                    min_package: Number(item.minPkg || 0),
                    is_available: item.inStock
                }
                this.stockFormError = ''
                this.showStockModal = true
            },
            async handleImageUpload(e) {
                const file = e.target.files[0]
                if (!file) return
                this.uploadingImage = true
                try {
                    const formData = new FormData()
                    formData.append('image', file)
                    const res = await fetch('api/upload_menu_image.php', { method: 'POST', body: formData })
                    if (res.status === 401 && this.handleAuthExpired) { this.handleAuthExpired(); return }
                    const data = await res.json()
                    if (data.status !== 'success') { alert(data.message || 'อัปโหลดไม่สำเร็จ'); return }
                    this.stockForm.image_url = data.image_url
                } catch (err) {
                    alert('เชื่อมต่อเซิร์ฟเวอร์ไม่สำเร็จ')
                } finally {
                    this.uploadingImage = false
                    e.target.value = ''
                }
            },
            async saveStockItem() {
                if (!this.stockForm.name.trim() || !this.stockForm.category_id) {
                    this.stockFormError = 'กรุณากรอกชื่อและเลือกหมวดหมู่'
                    return
                }
                const url = this.stockModalMode === 'add' ? 'api/add_menu_item.php' : 'api/update_menu_item.php'
                try {
                    const data = await this.apiPost(url, this.stockForm)
                    if (data.status !== 'success') { this.stockFormError = data.message || 'บันทึกไม่สำเร็จ'; return }
                    this.showStockModal = false
                    await this.fetchMenuItems()
                } catch (e) {
                    this.stockFormError = 'เชื่อมต่อเซิร์ฟเวอร์ไม่สำเร็จ'
                }
            },
            async deleteStockItem(item) {
                if (!confirm(`ยืนยันการลบ "${item.name}" ออกจากระบบ?`)) return
                try {
                    const data = await this.apiPost('api/delete_menu_item.php', { id: item.id })
                    if (data.status !== 'success') { alert(data.message || 'ลบไม่สำเร็จ'); return }
                    await this.fetchMenuItems()
                } catch (e) {
                    alert('เชื่อมต่อเซิร์ฟเวอร์ไม่สำเร็จ')
                }
            },
            openCategoryModal() {
                this.resetCategoryForm()
                this.showCategoryModal = true
            },
            resetCategoryForm() {
                this.categoryForm = { name: '', description: '' }
                this.categoryEditId = null
                this.categoryFormError = ''
            },
            editCategory(cat) {
                this.categoryForm = { name: cat.name, description: cat.description || '' }
                this.categoryEditId = cat.id
                this.categoryFormError = ''
            },
            async saveCategory() {
                if (!this.categoryForm.name.trim()) {
                    this.categoryFormError = 'กรุณากรอกชื่อหมวดหมู่'
                    return
                }
                const url = this.categoryEditId ? 'api/update_category.php' : 'api/add_category.php'
                const payload = this.categoryEditId ? { id: this.categoryEditId, ...this.categoryForm } : this.categoryForm
                try {
                    const data = await this.apiPost(url, payload)
                    if (data.status !== 'success') { this.categoryFormError = data.message || 'บันทึกไม่สำเร็จ'; return }
                    this.resetCategoryForm()
                    await this.fetchCategories()
                } catch (e) {
                    this.categoryFormError = 'เชื่อมต่อเซิร์ฟเวอร์ไม่สำเร็จ'
                }
            },
            async deleteCategory(cat) {
                if (!confirm(`ยืนยันการลบหมวดหมู่ "${cat.name}"?`)) return
                try {
                    const data = await this.apiPost('api/delete_category.php', { id: cat.id })
                    if (data.status !== 'success') { alert(data.message || 'ลบไม่สำเร็จ'); return }
                    await this.fetchCategories()
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
                        categoryId: m.category_id,
                        name: m.name,
                        category: m.category_name,
                        description: m.description || '',
                        price: Number(m.price || 0),
                        minPkg: String(m.min_package || 0),
                        emoji: m.emoji || '🍽️',
                        image: m.image_url || '',
                        inStock: !!Number(m.is_available)
                    }))
                }
            },
            async fetchCategories() {
                const data = await this.apiGet('api/get_categories.php')
                if (data.status === 'success') this.categories = data.data
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
                    const pendingIds = new Set(this.orders.filter(o => o.status === 'pending').map(o => o.id))
                    if (this.knownPendingOrderIds !== null && [...pendingIds].some(id => !this.knownPendingOrderIds.has(id))) {
                        this.playNotificationSound()
                    }
                    this.knownPendingOrderIds = pendingIds
                }
            },
            async fetchServiceCalls() {
                const data = await this.apiGet('api/get_service_calls.php')
                if (data.status === 'success') {
                    this.serviceCalls = data.data
                    const callIds = new Set(this.serviceCalls.map(c => c.id))
                    if (this.knownServiceCallIds !== null && [...callIds].some(id => !this.knownServiceCallIds.has(id))) {
                        this.playNotificationSound()
                    }
                    this.knownServiceCallIds = callIds
                }
            },
            playNotificationSound() {
                try {
                    const ctx = new (window.AudioContext || window.webkitAudioContext)()
                    const playTone = (freq, startTime, duration) => {
                        const osc = ctx.createOscillator()
                        const gain = ctx.createGain()
                        osc.type = 'sine'
                        osc.frequency.value = freq
                        gain.gain.setValueAtTime(0.15, startTime)
                        gain.gain.exponentialRampToValueAtTime(0.001, startTime + duration)
                        osc.connect(gain)
                        gain.connect(ctx.destination)
                        osc.start(startTime)
                        osc.stop(startTime + duration)
                    }
                    const now = ctx.currentTime
                    playTone(880, now, 0.15)
                    playTone(1100, now + 0.15, 0.2)
                } catch (e) {}
            },
            async refreshAll() {
                await Promise.all([this.fetchTables(), this.fetchOrders(), this.fetchServiceCalls()])
            },
            async initStaffData() {
                await Promise.all([this.fetchMenuItems(), this.fetchTables(), this.fetchCategories()])
                await Promise.all([this.fetchOrders(), this.fetchServiceCalls()])
                this.fetchLanUrl()
                this.startTimer()
                this.pollTimer = setInterval(() => this.refreshAll(), 5000)
            },
            async fetchLanUrl() {
                try {
                    const data = await this.apiGet('api/get_lan_ip.php')
                    if (data.status !== 'success') return
                    const basePath = window.location.pathname.replace(/staff\.php$/, '')
                    const port = window.location.port && window.location.port !== '80' ? `:${window.location.port}` : ''
                    this.lanUrl = `http://${data.ip}${port}${basePath}`
                } catch (e) { /* เงียบไว้ ไม่ใช่ฟีเจอร์หลัก ไม่ต้องรบกวนพนักงานด้วย error */ }
            },
            copyLanUrl() {
                navigator.clipboard.writeText(this.lanUrl).then(() => {
                    this.lanUrlCopied = true
                    setTimeout(() => { this.lanUrlCopied = false }, 1500)
                })
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
