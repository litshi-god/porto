# Deploy ke Vercel

Project ini sudah dikonfigurasi sebagai static site untuk Vercel.

## Lewat dashboard Vercel

1. Push folder ini ke repository GitHub/GitLab/Bitbucket.
2. Import repository di Vercel.
3. Pakai pengaturan default:
   - Framework Preset: `Other`
   - Build Command: `npm run build`
   - Output Directory: `.`
4. Deploy.

## Lewat Vercel CLI

```bash
npm install -g vercel
vercel
vercel --prod
```

## Catatan file besar

File installer `.exe` di folder `product/**/releases` tidak ikut deploy karena ukurannya besar dan lebih cocok disimpan di GitHub Releases, Cloudflare R2, S3, atau storage download lain. Setelah dipindahkan, update link download di halaman produk.
