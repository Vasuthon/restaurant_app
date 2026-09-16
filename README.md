# ชาบูหมาล่าบุฟเฟต์ — Buffet POS System

ระบบสั่งอาหารผ่าน QR Code สำหรับร้านชาบูบุฟเฟต์ แบ่งเป็น 2 ส่วน:

- **หน้าบ้าน** ([index.php](index.php)) — ลูกค้าสแกน QR ที่โต๊ะเพื่อดูเมนู สั่งอาหาร เรียกพนักงาน และดูบิล
- **หลังบ้าน** ([staff.php](staff.php)) — พนักงานเปิด/ปิดโต๊ะ ดูแดชบอร์ด จัดการออเดอร์ และครัวอัปเดตสถานะอาหาร/สต็อก (ป้องกันด้วย PIN)

## Tech Stack

- PHP 8.2 + Apache (REST API แบบไฟล์เดี่ยวใน [api/](api))
- MySQL 8.0
- Vue 3 และ Tailwind CSS (โหลดผ่าน CDN ไม่มีขั้นตอน build)
- Docker Compose สำหรับรันทั้งระบบ

## โครงสร้างโปรเจกต์

```
index.php          หน้าลูกค้า (หน้าบ้าน)
staff.php          หน้าพนักงาน/ครัว (หลังบ้าน, ต้องใส่ PIN)
api/               API endpoints (PHP)
config/            การเชื่อมต่อฐานข้อมูล (db.php) และตรวจสอบสิทธิ์พนักงาน (auth.php)
partials/head.php  ส่วน <head> ที่ใช้ร่วมกันทั้งสองหน้า
assets/shared.js   ฟังก์ชัน JS ที่ใช้ร่วมกันทั้งสองหน้า
schema.sql         โครงสร้างตารางฐานข้อมูล + ข้อมูลตัวอย่าง (seed)
```

## ติดตั้งและรันด้วย Docker (แนะนำ)

### สิ่งที่ต้องมี

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (มี Docker Compose มาให้แล้ว)

### ขั้นตอน

1. คัดลอกไฟล์ตัวอย่าง env แล้วแก้ค่าตามต้องการ:

   ```bash
   cp .env.example .env
   ```

   | ตัวแปร | ความหมาย |
   |---|---|
   | `DB_ROOT_PASSWORD` | รหัสผ่าน root ของ MySQL |
   | `DB_NAME` | ชื่อฐานข้อมูล |
   | `DB_USER` / `DB_PASSWORD` | ผู้ใช้ฐานข้อมูลที่แอปใช้เชื่อมต่อ |
   | `STAFF_PIN` | PIN สำหรับเข้าหน้าพนักงาน/ครัว ([staff.php](staff.php)) |

2. สั่งรัน:

   ```bash
   docker compose up -d --build
   ```

   ครั้งแรกที่รัน MySQL จะสร้างฐานข้อมูลและ import [schema.sql](schema.sql) (มีข้อมูลตัวอย่างให้แล้ว: โต๊ะ 4 โต๊ะ, หมวดหมู่ 5 หมวด, เมนู 10 รายการ) ให้อัตโนมัติ

3. เข้าใช้งาน:

   - หน้าลูกค้า: <http://localhost:8080>
   - หน้าพนักงาน/ครัว: <http://localhost:8080/staff.php>
   - phpMyAdmin (จัดการฐานข้อมูล): <http://localhost:8081> — ล็อกอินด้วย `DB_USER`/`DB_PASSWORD` (หรือ `root`/`DB_ROOT_PASSWORD` สำหรับสิทธิ์เต็ม) จาก `.env`

4. หยุดระบบ:

   ```bash
   docker compose down
   ```

   คำสั่งนี้ **ไม่ลบข้อมูลในฐานข้อมูล** เพราะข้อมูลถูกเก็บไว้ใน Docker volume (`db_data`) แยกต่างหาก ครั้งต่อไปที่ `docker compose up -d` ข้อมูลเดิมจะยังอยู่ครบ

   หากต้องการล้างข้อมูลฐานข้อมูลทั้งหมดเพื่อเริ่มใหม่ ให้ใช้:

   ```bash
   docker compose down -v
   ```

## วิธีใช้งาน

1. เข้า [staff.php](staff.php) แล้วใส่ PIN (`STAFF_PIN` ใน `.env`) เพื่อเข้าสู่ระบบพนักงาน
2. ไปที่แท็บ **ผังโต๊ะทั้งหมด** แล้วกด **เปิดโต๊ะใหม่** เลือกแพ็กเกจ/จำนวนคน ระบบจะสร้าง QR Code ประจำโต๊ะให้
3. ให้ลูกค้าสแกน QR Code นั้น (หรือกด "เปิดดูหน้ามือถือลูกค้า" เพื่อเปิดดูเองในแท็บใหม่) จะเข้าสู่หน้าสั่งอาหารของโต๊ะนั้นโดยตรง
4. ลูกค้าสั่งอาหารจากเมนู → ออเดอร์จะเข้าไปที่แท็บ **ครัว KDS** ในหน้าพนักงาน ให้ครัวกดเปลี่ยนสถานะ "กำลังปรุง" → "เสิร์ฟแล้ว"
5. เมื่อลูกค้าทานเสร็จ กลับไปที่ **ผังโต๊ะทั้งหมด** แล้วกด **เคลียร์** เพื่อปิดโต๊ะ

> หมายเหตุ: ถ้าจะให้ลูกค้าสแกน QR จากมือถือจริง เครื่องที่รัน Docker กับมือถือลูกค้าต้องอยู่ใน Wi-Fi/เครือข่ายเดียวกัน

## รันแบบไม่ใช้ Docker (XAMPP)

หากต้องการรันบน XAMPP โดยตรงแทน Docker:

1. วางโปรเจกต์ไว้ใน `htdocs` (เช่น `C:\xampp\htdocs\restaurant_app`)
2. สร้างฐานข้อมูลด้วย [schema.sql](schema.sql) ผ่าน phpMyAdmin หรือ `mysql -u root < schema.sql`
3. แก้ค่าเริ่มต้นการเชื่อมต่อฐานข้อมูลใน [config/db.php](config/db.php) หรือกำหนด environment variable ให้ตรงกับฐานข้อมูลที่สร้างไว้ (`DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASSWORD`)
4. กำหนด environment variable `STAFF_PIN` ให้ Apache/PHP เห็นค่า (เช่นผ่าน `SetEnv` ใน Apache config) มิฉะนั้นจะใช้ค่า default `1234`
5. เปิด Apache + MySQL จาก XAMPP Control Panel แล้วเข้า `http://localhost/restaurant_app/`
