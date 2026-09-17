<!DOCTYPE html>
<html lang="th">
<head>
    <?php $pageTitle = 'HOTPOT MAN - สั่งอาหารผ่าน QR'; include __DIR__ . '/partials/head.php'; ?>

    <style id="readable-dark-mala">
        .mobile-viewport {
            width: min(430px, 100%);
            height: min(850px, calc(100vh - 120px));
            min-height: 620px;
            margin: 0 auto;
            border-radius: 32px;
            border: 6px solid #A31E22 !important;
            background: #FFFDF9 !important;
            overflow: hidden;
            position: relative;
            box-shadow: 0 25px 60px rgba(38, 25, 15, 0.18);
        }

        .customer-shell { height: 100%; display: flex; flex-direction: column; background: #FFFDF9; }
        .customer-top { background: linear-gradient(135deg, #A31E22 0%, #7E1418 100%); border-bottom: 1px solid rgba(163,30,34,.25); padding: 15px; position: relative; }
        .brand-mark { width: 40px; height: 40px; border-radius: 12px; background: #FFFDF9; display: grid; place-items: center; font-size: 18px; color: #A31E22; }
        .brand-name { font-size: 15px; font-weight: 800; color: #FFF; }
        .brand-sub { font-size: 9px; color: rgba(255,255,255,.8); }

        .table-chip { background: rgba(255,255,255,.18); border: 1px solid rgba(255,255,255,.4); color: #FFF; border-radius: 99px; padding: 4px 10px; font-size: 11px; font-weight: 700; }
        .timer-chip { background: #FBEEEC; border: 1px solid #E9CFC9; border-radius: 12px; padding: 8px 12px; margin-top: 8px; display: flex; justify-content: space-between; align-items: center; }
        .timer-chip .time { font-size: 20px; font-weight: 800; color: #1C1815; }
        .timer-chip.warning { background: #F6DEDA; border-color: #A31E22; }

        .customer-body { flex: 1; overflow-y: auto; padding: 16px 14px 90px; background: #F7F2EA; }
        .hero-card { background: linear-gradient(145deg, #FBEEEC, #FFFDF9); border: 1px solid #E9CFC9; border-radius: 20px; padding: 18px; color: #1C1815; box-shadow: 0 4px 14px rgba(38,25,15,.06); }
        .section-title { font-size: 15px; font-weight: 800; color: #1C1815; display: flex; align-items: center; gap: 8px; }
        .section-title:before { content: ""; width: 4px; height: 18px; border-radius: 4px; background: #A31E22; }

        .search-box input { width: 100%; background: #F1EBE0; border: 1px solid #E8DFCF; border-radius: 12px; padding: 10px 12px; font-size: 12px; color: #1C1815; outline: none; }
        .cat-row { display: flex; gap: 8px; overflow-x: auto; padding-bottom: 4px; }
        .cat-btn { white-space: nowrap; border: 1px solid #E8DFCF; background: #FFFDF9; color: #7A7266; border-radius: 99px; padding: 7px 14px; font-size: 11px; font-weight: 700; }
        .cat-btn.active { background: #A31E22; color: #FFF; border-color: #A31E22; }

        .menu-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; }
        .food-card { background: #FFFDF9; border: 1px solid #EEE6D8; border-radius: 16px; overflow: hidden; display: flex; flex-direction: column; box-shadow: 0 3px 10px rgba(38,25,15,.06); transform: translateZ(0); }
        .food-visual { height: 110px; position: relative; background: #F1EBE0; overflow: hidden; display: grid; place-items: center; }
        .food-visual img { width: 100%; height: 100%; object-fit: cover; }
        .food-art { font-size: 48px; }
        .food-badge { position: absolute; top: 6px; left: 6px; background: rgba(255,253,249,0.94); border: 1px solid #EEE6D8; border-radius: 99px; padding: 3px 7px; font-size: 8px; font-weight: 800; color: #9A3E12; z-index: 2; }
        .lock-overlay { position: absolute; inset: 0; background: rgba(255,253,249,0.9); display: grid; place-items: center; color: #1C1815; font-size: 10px; font-weight: 800; z-index: 3; }
        .food-body { padding: 10px; flex: 1; display: flex; flex-direction: column; justify-content: space-between; background: #FFFDF9; }
        .food-name { font-size: 12px; font-weight: 800; color: #1C1815; line-height: 1.4; min-height: 34px; }
        .add-btn { width: 32px; height: 32px; border-radius: 10px; background: #A31E22; color: #FFF; font-weight: 800; border: none; font-size: 16px; cursor: pointer; }

        .cart-card, .bill-card, .order-card, .service-card { background: #FFFDF9; border: 1px solid #EEE6D8; border-radius: 16px; padding: 12px; box-shadow: 0 3px 10px rgba(38,25,15,.06); }
        .primary-btn { width: 100%; border: none; border-radius: 14px; padding: 12px; font-size: 13px; font-weight: 800; color: #FFF; background: #A31E22; cursor: pointer; box-shadow: 0 8px 20px rgba(163,30,34,.22); }

        .bottom-nav { position: absolute; bottom: 0; left: 0; right: 0; background: rgba(255, 253, 249, 0.97); border-top: 1px solid #EEE6D8; display: grid; grid-template-columns: repeat(4, 1fr); padding: 8px 4px 12px; z-index: 30; }
        .nav-btn { border: none; background: transparent; color: #8F8676; font-size: 10px; font-weight: 700; display: flex; flex-direction: column; align-items: center; gap: 4px; cursor: pointer; }
        .nav-btn i { font-size: 18px; }
        .nav-btn.active { color: #A31E22; }
        .nav-count { position: absolute; top: 2px; right: 22px; background: #A31E22; color: #FFF; width: 16px; height: 16px; border-radius: 50%; display: grid; place-items: center; font-size: 8px; font-weight: 800; }
    </style>
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
                        HOTPOT MAN <span class="bg-accent-gold/20 text-accent-gold text-xs px-2 py-0.5 rounded border border-accent-gold/40 font-mono">BUFFET • 2 HRS</span>
                    </h1>
                    <p class="text-[11px] md:text-xs text-[#7A7266]">ระบบสั่งอาหารผ่าน QR</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2 md:gap-3 w-full sm:w-auto justify-end">
            <button type="button" @click="resetDemoData" class="text-xs text-[#7A7266] hover:text-red-400 bg-[#F1EBE0] p-2 md:px-3 md:py-2 rounded-lg border border-[#E8DFCF] transition-colors flex items-center gap-1 cursor-pointer shrink-0">
                <i class="fa-solid fa-rotate-right"></i> <span class="hidden md:inline">รีเซ็ต</span>
            </button>
            <a href="staff.php" class="text-xs text-[#8F8676] hover:text-[#1C1815] bg-[#F1EBE0] p-2 md:px-3 md:py-2 rounded-lg border border-[#E8DFCF] transition-colors flex items-center gap-1 cursor-pointer shrink-0">
                <i class="fa-solid fa-user-tie"></i> <span class="hidden md:inline">พนักงาน/ครัว</span>
            </a>
        </div>
    </header>

    <!-- MAIN APP CONTAINER -->
    <main class="flex-grow p-2 sm:p-4 md:p-6 flex items-center justify-center relative">

        <!-- 📱 CUSTOMER MOBILE INTERFACE -->
        <div class="mobile-viewport">
          <div class="customer-shell">

            <div class="customer-top">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                  <div class="brand-mark"><i class="fa-solid fa-pepper-hot"></i></div>
                  <div>
                    <div class="brand-name">HOTPOT MAN</div>
                    <div class="brand-sub">HOTPOT MAN • ALL YOU CAN EAT</div>
                  </div>
                </div>
                <div v-if="activeTable" class="table-chip">โต๊ะ {{ activeTable.name }}</div>
              </div>

              <div v-if="activeTable && activeTable.status === 'occupied'" class="mt-2 text-[11px] text-[#B8860B] font-bold">
                <i class="fa-solid fa-bowl-food mr-1"></i>{{ activeTable.packageName }} · {{ activeTable.adults }} ผู้ใหญ่ / {{ activeTable.children }} เด็ก
              </div>

              <div v-if="activeTable && activeTable.status === 'occupied'" class="timer-chip" :class="{warning: activeTable.timeLeft <= 1800}">
                <div>
                  <div class="text-[10px] text-[#5C5148]">เวลาทานคงเหลือ</div>
                  <div class="time"><i class="fa-regular fa-clock mr-1 text-sm"></i>{{ formatTimeLeft(activeTable.timeLeft) }}</div>
                </div>
                <div class="text-right">
                  <div class="text-[10px] text-[#5C5148]">จำกัดเวลา</div>
                  <div class="text-xs font-bold text-[#B8860B]">2 ชั่วโมง</div>
                </div>
              </div>
            </div>

            <div class="customer-body">

              <!-- Customer access requires session from table QR -->
              <div v-if="custStep === 'session-required'" class="space-y-4">
                <div class="hero-card">
                  <div class="text-[11px] text-[#B8860B] font-bold tracking-wider mb-1">TABLE SESSION REQUIRED</div>
                  <div class="text-2xl font-black mb-2">สแกน QR Code<br>ที่โต๊ะเพื่อเริ่มสั่งอาหาร</div>
                  <div class="text-xs text-[#5C5148] leading-relaxed">กรุณาแจ้งพนักงานเพื่อเปิดโต๊ะและรับ QR Code ประจำโต๊ะของคุณในการสั่งอาหาร</div>
                </div>
                <div class="cart-card text-center text-xs text-[#7A7266]">
                  <i class="fa-solid fa-qrcode text-3xl mb-2 text-accent-gold block"></i>
                  คุณยังไม่ได้สแกน QR Code ประจำโต๊ะ หรือโต๊ะนี้ยังไม่ได้เปิดใช้งาน
                </div>
              </div>

              <!-- MENU -->
              <div v-if="custStep === 'menu'" class="space-y-3">
                <div>
                  <div class="section-title">รายการเมนู</div>
                  <div class="text-[10px] text-[#5C5148] mt-0.5">เลือกเมนูได้ไม่อั้นตามแพ็กเกจของคุณ</div>
                </div>

                <div class="search-box">
                  <input v-model="searchQuery" placeholder="ค้นหาเนื้อ หมู ซีฟู้ด ผัก...">
                </div>

                <div class="cat-row">
                  <button v-for="cat in categories" :key="cat" @click="activeCat=cat" class="cat-btn" :class="{active:activeCat===cat}">{{ cat }}</button>
                </div>

                <div class="menu-grid">
                  <div v-for="item in filteredMenuItems" :key="item.id" class="food-card">
                    <div class="food-visual">
                      <span class="food-badge">{{ isItemLocked(item) ? 'แพ็กเกจอัปเกรด' : 'สั่งได้' }}</span>
                      <img v-if="menuVisualMode === 'photo' && item.image" :src="item.image" :alt="item.name" @error="item.image=''">
                      <div v-else class="food-art"><span>{{ item.emoji }}</span></div>
                      <div v-if="!item.inStock" class="lock-overlay">สินค้าหมด</div>
                      <div v-else-if="isItemLocked(item)" class="lock-overlay"><i class="fa-solid fa-lock mr-1"></i> {{ item.minPkg }}+</div>
                    </div>
                    <div class="food-body">
                      <div class="food-name">{{ item.name }}</div>
                      <div class="flex items-center justify-between mt-2">
                        <span class="text-[9px] text-[#5C5148] font-bold">{{ item.inStock ? 'บุฟเฟต์' : 'หมด' }}</span>
                        <button v-if="!isItemLocked(item) && item.inStock" @click="addToCart(item)" class="add-btn">+</button>
                        <span v-else class="text-xs text-[#6B5F55]"><i class="fa-solid fa-lock"></i></span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- CART -->
              <div v-if="custStep === 'cart'" class="space-y-3">
                <div class="flex items-end justify-between">
                  <div>
                    <div class="section-title">ตะกร้าของฉัน</div>
                    <div class="text-[10px] text-[#5C5148] mt-0.5">โต๊ะ {{ activeTable?.name }} • รวม {{ cartTotalTrays }} ถาด</div>
                  </div>
                  <span class="text-2xl font-black text-accent-crimson">{{ cartTotalTrays }}</span>
                </div>

                <div v-if="cart.length===0" class="cart-card text-center py-8">
                  <i class="fa-solid fa-basket-shopping text-4xl text-accent-gold mb-3 block"></i>
                  <div class="text-xs text-[#5C5148]">ยังไม่มีรายการอาหารในตะกร้า</div>
                  <button @click="custStep='menu'" class="mt-3 text-accent-crimson font-bold text-xs">← เลือกเมนูสั่งอาหาร</button>
                </div>

                <div v-for="cItem in cart" :key="cItem.id" class="cart-card space-y-2">
                  <div class="flex justify-between items-start">
                    <div>
                      <div class="text-xs font-bold text-[#1C1815]">{{ cItem.name }}</div>
                      <div class="text-[10px] text-[#5C5148]">สั่งถาดละ 1 เสิร์ฟ</div>
                    </div>
                    <div class="font-black text-accent-crimson text-sm">{{ cItem.qty }}x</div>
                  </div>
                  <div class="flex items-center justify-between pt-1">
                    <div class="flex items-center gap-2 bg-white rounded-xl p-1 border border-[#E5D5D3]">
                      <button @click="cItem.qty>1 ? cItem.qty-- : removeFromCart(cItem.id)" class="w-7 h-7 rounded-lg bg-[#EEE6D8] text-[#1C1815] font-bold text-sm flex items-center justify-center">−</button>
                      <span class="w-6 text-center font-bold text-xs text-[#1C1815]">{{ cItem.qty }}</span>
                      <button @click="cItem.qty++" class="w-7 h-7 rounded-lg bg-accent-crimson text-white font-bold text-sm flex items-center justify-center">+</button>
                    </div>
                    <button @click="removeFromCart(cItem.id)" class="text-xs text-[#6B5F55] hover:text-red-400">ลบออก</button>
                  </div>
                  <input v-model="cItem.note" placeholder="หมายเหตุ เช่น ไม่รับผัก / เอาเผ็ดมาก" class="w-full bg-white border border-[#E5D5D3] rounded-xl px-3 py-1.5 text-xs text-[#1C1815] outline-none">
                </div>

                <button v-if="cart.length" @click="submitOrder" class="primary-btn mt-2">
                  ส่งออเดอร์เข้าครัว <i class="fa-solid fa-fire-burner ml-1"></i>
                </button>
              </div>

              <!-- TRACKER -->
              <div v-if="custStep === 'tracker'" class="space-y-3">
                <div>
                  <div class="section-title">เรียกบริการพนักงาน</div>
                  <div class="text-[10px] text-[#5C5148] mt-0.5">กดปุ่มเพื่อส่งสัญญาณเรียกพนักงานมาที่โต๊ะ</div>
                </div>

                <div class="grid grid-cols-2 gap-2">
                  <button @click="callWaiter('เติมน้ำซุปหมาล่า')" class="service-card text-left hover:border-accent-crimson transition-all">
                    <div class="text-2xl">🌶️</div>
                    <div class="text-xs font-bold mt-1 text-[#1C1815]">เติมซุปหมาล่า</div>
                  </button>
                  <button @click="callWaiter('เติมน้ำซุปกระดูกหมู')" class="service-card text-left hover:border-accent-crimson transition-all">
                    <div class="text-2xl">🍲</div>
                    <div class="text-xs font-bold mt-1 text-[#1C1815]">เติมซุปกระดูกหมู</div>
                  </button>
                  <button @click="callWaiter('เก็บถาดอาหารบนโต๊ะ')" class="service-card text-left hover:border-accent-crimson transition-all">
                    <div class="text-2xl">🧹</div>
                    <div class="text-xs font-bold mt-1 text-[#1C1815]">เก็บถาดคอนโด</div>
                  </button>
                  <button @click="callWaiter('เช็คบิลชำระเงิน')" class="service-card text-left hover:border-accent-crimson transition-all">
                    <div class="text-2xl">💳</div>
                    <div class="text-xs font-bold mt-1 text-[#1C1815]">เรียกเช็คบิล</div>
                  </button>
                </div>

                <div class="border-t border-[#E8DFCF] my-3"></div>

                <div class="section-title">รายการออเดอร์ที่สั่งไปแล้ว</div>
                <div v-if="myOrders.length===0" class="order-card text-center py-6 text-xs text-[#6B5F55]">
                  ยังไม่มีประวัติการสั่งอาหาร
                </div>
                <div v-for="ord in myOrders" :key="ord.id" class="order-card space-y-2 mb-2">
                  <div class="flex justify-between items-center pb-1 border-b border-[#E8DFCF]">
                    <span class="text-[10px] text-[#5C5148]">#{{ ord.id }} • {{ ord.time }}</span>
                    <span :class="getStatusBadgeClass(ord.status)" class="text-[10px] font-bold px-2 py-0.5 rounded-full">
                      {{ getStatusText(ord.status) }}
                    </span>
                  </div>
                  <div v-for="item in ord.items" :key="item.id" class="flex justify-between items-center text-xs text-[#1C1815]">
                    <span>{{ item.name }}</span>
                    <span v-if="item.unavailable" class="text-[9px] font-bold px-2 py-0.5 rounded-full bg-red-50 text-red-700 border border-red-200">ของหมด ขออภัย</span>
                    <b v-else>{{ item.qty }} ถาด</b>
                  </div>
                </div>
              </div>

              <!-- BILL -->
              <div v-if="custStep === 'bill'" class="space-y-3">
                <div>
                  <div class="section-title">สรุปยอดชำระเงิน</div>
                  <div class="text-[10px] text-[#5C5148] mt-0.5">โต๊ะ {{ activeTable?.name }} • แพ็กเกจ {{ activeTable?.packageName }}</div>
                </div>

                <div class="bill-card space-y-2">
                  <div class="flex justify-between text-xs text-[#1C1815] py-1">
                    <span>ผู้ใหญ่ {{ activeTable?.adults }} ท่าน × {{ formatNumber(activeTable?.packagePrice) }}฿</span>
                    <b>{{ formatNumber((activeTable?.adults||0)*(activeTable?.packagePrice||0)) }} ฿</b>
                  </div>
                  <div v-if="activeTable?.children>0" class="flex justify-between text-xs text-[#1C1815] py-1 border-t border-[#E8DFCF]">
                    <span>เด็ก {{ activeTable?.children }} ท่าน (ลด 50%)</span>
                    <b>{{ formatNumber((activeTable?.children||0)*(activeTable?.packagePrice||0)*.5) }} ฿</b>
                  </div>
                  <div class="flex justify-between text-xs text-[#5C5148] py-1 border-t border-[#E8DFCF]">
                    <span>รวมอาหารสั่งทั้งหมด</span>
                    <b>{{ totalTraysOrderedByTable(activeTable?.id) }} ถาด (ฟรี)</b>
                  </div>
                  <div class="border-t border-[#E5D5D3] pt-2 flex justify-between items-end">
                    <span class="text-xs font-bold text-[#1C1815]">ยอดสุทธิรวม</span>
                    <span class="text-2xl font-black text-accent-gold">{{ formatNumber(calculateSessionTotal()) }} <small class="text-xs">฿</small></span>
                  </div>
                </div>

                <button @click="callWaiter('เรียกพนักงานมาเช็คบิลและรับชำระเงิน')" class="primary-btn !bg-gradient-to-r !from-accent-gold !to-yellow-600 !text-black font-extrabold">
                  <i class="fa-solid fa-bell mr-1"></i> เรียกพนักงานมาเช็คบิลที่โต๊ะ
                </button>
              </div>

            </div>

            <!-- BOTTOM NAVIGATION BAR -->
            <div v-if="['menu','cart','tracker','bill'].includes(custStep)" class="bottom-nav">
              <button @click="custStep='menu'" class="nav-btn" :class="{active:custStep==='menu'}">
                <i class="fa-solid fa-bowl-food"></i>เมนู
              </button>
              <button @click="custStep='cart'" class="nav-btn relative" :class="{active:custStep==='cart'}">
                <span v-if="cartTotalTrays>0" class="nav-count">{{ cartTotalTrays }}</span>
                <i class="fa-solid fa-basket-shopping"></i>ตะกร้า
              </button>
              <button @click="custStep='tracker'" class="nav-btn" :class="{active:custStep==='tracker'}">
                <i class="fa-solid fa-bell"></i>สถานะ
              </button>
              <button @click="custStep='bill'" class="nav-btn" :class="{active:custStep==='bill'}">
                <i class="fa-solid fa-receipt"></i>บิล
              </button>
            </div>

          </div>
        </div>

    </main>

</div>

<!-- SHARED APP METHODS -->
<script src="assets/shared.js"></script>

<!-- VUE 3 APP SCRIPT -->
<script>
    const { createApp } = Vue

    createApp({
        data() {
            return {
                custStep: 'session-required',
                menuVisualMode: 'photo',
                searchQuery: '',
                activeCat: 'ทั้งหมด',

                activeTableId: null,

                categories: ['ทั้งหมด'],
                menuItems: [],
                tables: [],

                cart: [],
                orders: [],

                timer: null,
                pollTimer: null
            }
        },
        computed: {
            activeTable() {
                return this.tables.find(t => t.id === this.activeTableId)
            },
            filteredMenuItems() {
                return this.menuItems.filter(item => {
                    const matchCat = this.activeCat === 'ทั้งหมด' || item.category === this.activeCat
                    const matchSearch = item.name.toLowerCase().includes(this.searchQuery.toLowerCase())
                    return matchCat && matchSearch
                })
            },
            cartTotalTrays() {
                return this.cart.reduce((sum, item) => sum + item.qty, 0)
            },
            myOrders() {
                return this.orders.filter(o => o.tableId === this.activeTableId)
            }
        },
        methods: {
            ...SharedAppMethods,
            isItemLocked(item) {
                if (!this.activeTable || this.activeTable.status !== 'occupied') return false
                const pkgLevel = parseInt(this.activeTable.packageId || '299')
                const itemLevel = parseInt(item.minPkg || '299')
                return itemLevel > pkgLevel
            },
            addToCart(item) {
                const existing = this.cart.find(c => c.id === item.id)
                if (existing) {
                    existing.qty++
                } else {
                    this.cart.push({ id: item.id, name: item.name, qty: 1, note: '' })
                }
            },
            removeFromCart(itemId) {
                this.cart = this.cart.filter(c => c.id !== itemId)
            },
            async submitOrder() {
                if (this.cart.length === 0 || !this.activeTable) return
                const summary = this.cart.map(c => `${c.name} x${c.qty}`).join('\n')
                if (!confirm(`ยืนยันส่งออเดอร์เข้าครัว?\n\n${summary}`)) return
                const items = this.cart.map(c => ({ menu_item_id: c.id, quantity: c.qty, note: c.note || '' }))
                try {
                    const data = await this.apiPost('api/create_order.php', {
                        session_id: this.activeTable.sessionId,
                        items
                    })
                    if (data.status !== 'success') { alert(data.message || 'สั่งอาหารไม่สำเร็จ'); return }

                    this.cart = []
                    this.custStep = 'tracker'
                    await this.fetchOrders()
                } catch (e) {
                    alert('เชื่อมต่อเซิร์ฟเวอร์ไม่สำเร็จ')
                }
            },
            async callWaiter(reason) {
                if (!this.activeTable) return
                if (!confirm(`ยืนยันเรียกพนักงาน: "${reason}"?`)) return
                try {
                    const data = await this.apiPost('api/call_waiter.php', {
                        session_id: this.activeTable.sessionId,
                        reason
                    })
                    if (data.status !== 'success') { alert(data.message || 'แจ้งพนักงานไม่สำเร็จ'); return }
                    alert(`[ส่งสัญญาณเรียบร้อย] พนักงานกำลังไปที่โต๊ะ ${this.activeTable?.name} สำหรับ: ${reason}`)
                } catch (e) {
                    alert('เชื่อมต่อเซิร์ฟเวอร์ไม่สำเร็จ')
                }
            },
            totalTraysOrderedByTable(tableId) {
                return this.orders
                    .filter(o => o.tableId === tableId)
                    .reduce((sum, ord) => sum + ord.items.reduce((iSum, i) => iSum + i.qty, 0), 0)
            },
            calculateSessionTotal() {
                if (!this.activeTable) return 0
                const adultsTotal = (this.activeTable.adults || 0) * (this.activeTable.packagePrice || 0)
                const childrenTotal = (this.activeTable.children || 0) * (this.activeTable.packagePrice || 0) * 0.5
                return adultsTotal + childrenTotal
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
            async fetchCategories() {
                const data = await this.apiGet('api/get_categories.php')
                if (data.status === 'success') {
                    this.categories = ['ทั้งหมด', ...data.data.map(c => c.name)]
                }
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
                if (!this.activeTable || !this.activeTable.sessionId) return
                const data = await this.apiGet(`api/get_orders.php?session_id=${encodeURIComponent(this.activeTable.sessionId)}`)
                if (data.status === 'success') {
                    this.orders = data.data.map(o => ({
                        id: o.id,
                        tableId: o.table_id,
                        time: new Date(o.created_at.replace(' ', 'T')).toLocaleTimeString('th-TH', { hour: '2-digit', minute: '2-digit' }),
                        status: o.status,
                        items: o.items.map(i => ({ id: i.menu_item_id, name: i.name, qty: Number(i.quantity), note: i.note || '', unavailable: i.item_status === 'unavailable' }))
                    }))
                }
            },
            async refreshAll() {
                await Promise.all([this.fetchTables(), this.fetchOrders()])
            }
        },
        async mounted() {
            await Promise.all([this.fetchCategories(), this.fetchMenuItems(), this.fetchTables()])

            const urlParams = new URLSearchParams(window.location.search)
            const tableParam = urlParams.get('table')

            if (tableParam) {
                const targetTable = this.tables.find(t => t.id == tableParam)
                if (targetTable) {
                    this.activeTableId = targetTable.id
                    this.custStep = targetTable.status === 'occupied' ? 'menu' : 'session-required'
                }
            }

            await this.fetchOrders()

            this.startTimer()
            this.pollTimer = setInterval(() => this.refreshAll(), 5000)
        },
        unmounted() {
            if (this.timer) clearInterval(this.timer)
            if (this.pollTimer) clearInterval(this.pollTimer)
        }
    }).mount('#app')
</script>

</body>
</html>
