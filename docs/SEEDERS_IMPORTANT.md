# Ecommerce Project – Important Seeder Files

একটি ইকমার্স প্রজেক্টে নিচের সিডারগুলো রাখা **গুরুত্বপূর্ণ** (ডেমো/ইনস্টল/CodeCanyon রিভিউয়ের জন্য)।

---

## ১. অতি প্রয়োজনীয় (App চালু ও ডেমো)

| Seeder | কাজ | না থাকলে কী হয় |
|--------|-----|------------------|
| **UserSeeder** | Admin, Customer, Staff একাউন্ট তৈরি | লগইন/অ্যাডমিন এক্সেস নেই |
| **SettingSeeder** | app_name, app_currency ইত্যাদি সেটিং | সাইট নাম/কারেন্সি ফাঁকা |
| **CurrencySeeder** | ডিফল্ট কারেন্সি (USD, BDT, EUR) | চেকআউট/প্রাইস কাজ করবে না |
| **LanguageSeeder** | ডিফল্ট ভাষা | মাল্টি-ল্যাংগুয়েজ অফ থাকবে |
| **CategorySeeder** | ক্যাটাগরি + সাবক্যাটাগরি | শপ/হোম ক্যাটাগরি শূন্য |
| **BrandSeeder** | ব্র্যান্ড লিস্ট | প্রোডাক্টে ব্র্যান্ড সিলেক্ট করা যাবে না |
| **ProductSeeder** | ডেমো প্রোডাক্ট | হোম/শপ পেজ ফাঁকা |
| **PageSeeder** | About, Contact, Terms, Privacy পেজ | ফুটার/মেনু লিংক ভাঙবে |

---

## ২. প্রোডাক্ট/অর্ডার ফিচার এর জন্য

| Seeder | কাজ | না থাকলে |
|--------|-----|-----------|
| **UnitSeeder** | পিস, Kg, L ইত্যাদি ইউনিট | প্রোডাক্ট এডিটে ইউনিট ড্রপডাউন খালি |
| **ColorSeeder** | কালার লিস্ট (Red, Blue ইত্যাদি) | প্রোডাক্টে কালার অপশন নেই |
| **SizeSeeder** | XS, S, M, L, XL ইত্যাদি | সাইজ ভিত্তিক প্রোডাক্ট এডিট করা যায় না |
| **RefundReasonSeeder** | রিফান্ড কারণ (Damaged, Wrong item ইত্যাদি) | রিফান্ড রিকোয়েস্ট সাবমিট করতে কারণ সিলেক্ট করা যাবে না |
| **SupportDepartmentSeeder** | সাপোর্ট ডিপার্টমেন্ট (Sales, Technical) | সাপোর্ট টিকেট খুলতে ডিপার্টমেন্ট বেছে নেওয়া যাবে না |

---

## ৩. অপশনাল কিন্তু ভালো থাকা (ডেমো/রিভিউ)

| Seeder | কাজ |
|--------|-----|
| **SettingSeeder** (বিস্তৃত) | হোম হিরো টাইটেল, সেকশন টেক্সট, প্রমো ইমেজ পাথ ইত্যাদি – ডেমো দেখানোর জন্য |
| **CouponSeeder** | একটা টেস্ট কুপন – চেকআউট টেস্ট করার জন্য |

---

## ৪. চালানোর সঠিক অর্ডার (DatabaseSeeder)

নিচের অর্ডার মেনে চলা ভালো (dependency অনুযায়ী):

1. **UserSeeder** – সব আগে (অ্যাডমিন/কাস্টমার)
2. **SettingSeeder** – অ্যাপ সেটিং
3. **CurrencySeeder** – কারেন্সি
4. **LanguageSeeder** – ভাষা
5. **UnitSeeder** – প্রোডাক্টের ইউনিট
6. **ColorSeeder** – প্রোডাক্টের কালার
7. **SizeSeeder** – প্রোডাক্টের সাইজ
8. **BrandSeeder** – ব্র্যান্ড
9. **CategorySeeder** – ক্যাটাগরি
10. **ProductSeeder** – প্রোডাক্ট (Category, Brand, Unit ওপর নির্ভর)
11. **RefundReasonSeeder** – রিফান্ড কারণ
12. **SupportDepartmentSeeder** – সাপোর্ট ডিপার্টমেন্ট
13. **PageSeeder** – স্ট্যাটিক পেজ (About, Terms ইত্যাদি)

---

## ৫. এই প্রজেক্টে যা আছে / আপডেট

- **UserSeeder** – Admin, Customer, Staff (ডেমো পাসওয়ার্ড পরিবর্তন করুন)
- **SettingSeeder** – app_name, app_currency, app_logo, app_favicon
- **UnitSeeder**, **ColorSeeder**, **SizeSeeder**, **RefundReasonSeeder**, **SupportDepartmentSeeder** – সব DatabaseSeeder এ কল হয়
- **ProductSeeder** – কেবল CategorySeeder এর **সঠিক** category/subcategory slug ব্যবহার করে; **unit_id** সেট করে; **is_featured** ও **is_flash_sale** স্পষ্টভাবে সেট যাতে হোমে Featured ও Flash Sale সেকশনে প্রোডাক্ট দেখায়
- **PageSeeder** – LŪXĒ/ডামি টেক্সট সরিয়ে **প্রজেক্ট-অনুগত** কন্টেন্ট (config('app.name') ব্যবহার); About, Terms, Privacy, Shipping – প্রফেশনাল ও জেনেরিক
- লগইন/রেজিস্টার/ফরগট/রিসেট পেজের টাইটেল ও ইমেজ **Website Setup** মেনু থেকে এডিট করা হয় (সিডার নয়)

ইনস্টল/ডেমোর পর একবার `php artisan migrate:fresh --seed` বা `php artisan db:seed` চালালে অ্যাপ ও হোম পেজে প্রোডাক্ট ঠিকভাবে দেখাবে।
