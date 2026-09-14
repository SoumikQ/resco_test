# 🚀 RESCO POS - Deploy to Render.com Step-by-Step Guide

এই গাইড ফলো করে আপনি খুব সহজেই **Render.com**-এ আপনার **RESCO Restaurant Management** সফটওয়্যারটি সম্পূর্ণ বিনামূল্যে লাইভ করতে পারবেন।

---

## 📋 প্রয়োজনীয় বিষয়সমূহ:
1. **GitHub Account** (প্রজেক্টের কোড GitHub-এ পুশ করা থাকতে হবে)।
2. **Render.com Account** (ফ্রি অ্যাকাউন্ট তৈরি করুন)।
3. **Cloud MySQL Database** (Render-এ MySQL সরাসরি ফ্রি না থাকলে [Aiven.io](https://aiven.io) বা [Clever-Cloud](https://www.clever-cloud.com) থেকে ফ্রি MySQL ডাটাবেজ ব্যবহার করতে পারেন)।

---

## 🛠️ লাইভ করার সহজ ৪টি স্টেপ:

### স্টেপ ১: কোড GitHub-এ Push করুন
আপনার প্রজেক্টের সব ফাইল গিটহাবে পুশ করুন:
```bash
git add .
git commit -m "Docker & Render deployment ready"
git push origin main
```

---

### স্টেপ ২: Render.com-এ প্রজেক্ট কানেক্ট করুন
1. [Render.com](https://dashboard.render.com/) এ লগইন করুন।
2. **New +** বাটনে ক্লিক করে **"Web Service"** সিলেক্ট করুন।
3. আপনার **GitHub Repository** (RESCO) সিলেক্ট করে **Connect** দিন।

---

### স্টেপ ৩: সার্ভিস কনফিগার করুন
- **Name:** `resco-pos` (বা আপনার পছন্দের নাম)
- **Region:** `Singapore` (ভারত/বাংলাদেশ থেকে সবচেয়ে ফাস্ট)
- **Runtime:** `Docker` (স্বয়ংক্রিয়ভাবে সিলেক্ট হবে)
- **Instance Type:** `Free`

---

### স্টেপ ৪: Environment Variables যোগ করুন
Render-এর **Environment Variables** সেকশনে নিচের ভ্যালুগুলো দিন:

| Key | Value | Description |
|---|---|---|
| `APP_NAME` | `RESCO Restaurant` | সফটওয়্যার নাম |
| `APP_ENV` | `production` | প্রোডাকশন মোড |
| `APP_DEBUG` | `false` | এরর হাইড রাখার জন্য |
| `APP_KEY` | *(Render-এ Generate Value অথবা আপনার লোকাল .env এর key দিন)* | সিকিউরিটি কি |
| `APP_URL` | `https://your-app-name.onrender.com` | আপনার Render লাইভ URL |
| `DB_CONNECTION` | `mysql` | ডাটাবেজ টাইপ |
| `DB_HOST` | *(আপনার ক্লাউড MySQL হোস্ট/সার্ভার IP)* | যেমন Aiven/Clever Cloud হোস্ট |
| `DB_PORT` | `3306` (বা আপনার ক্লাউড DB পোর্ট) | পোর্ট |
| `DB_DATABASE` | *(আপনার ক্লাউড DB নাম)* | ডাটাবেজ নাম |
| `DB_USERNAME` | *(আপনার ক্লাউড DB ইউজার)* | ইউজারনেম |
| `DB_PASSWORD` | *(আপনার ক্লাউড DB পাসওয়ার্ড)* | পাসওয়ার্ড |
| `SESSION_DRIVER` | `database` | সেশন ড্রাইভার |

---

### স্টেপ ৫: Create Web Service এ ক্লিক করুন!
- **Deploying...** শুরু হবে।
- Docker ইমেজ স্বয়ংক্রিয়ভাবে বিল্ড হবে, Node.js দিয়ে সব অ্যাসেট কম্পাইল হবে, ডাটাবেজ মাইগ্রেশন চলবে এবং Nginx + PHP-FPM স্টার্ট হবে।
- ২-৩ মিনিটের মধ্যে আপনার সাইট **LIVE** হয়ে যাবে এবং Render আপনাকে একটি ফ্রি HTTPS লাইভ লিঙ্ক দিয়ে দেবে (যেমন: `https://resco-pos.onrender.com`)! 🎉

---

## 💡 জরুরি কিছু টিপস:
- **ফ্রি MySQL ডাটাবেজ:** আপনি [Aiven Free MySQL](https://aiven.io/pricing) থেকে লাইফটাইম ফ্রি 5GB ক্লাউড MySQL ডাটাবেজ নিতে পারেন এবং সেই ক্রেডেনশিয়ালগুলো Render-এ দিয়ে দিতে পারেন।
- **স্বয়ংক্রিয় ডিপ্লয়:** গিটহাবে নতুন কোড পুশ করলেই Render নিজে থেকে নতুন ভার্সন লাইভ করে দেবে!
