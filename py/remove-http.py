# Membaca file url.txt
with open('hasil_pencarian.txt', 'r') as file:
    urls = file.readlines()

# Menghapus prefix yang tidak diinginkan
cleaned_urls = []
for url in urls:
    url = url.strip()  # Menghapus spasi di awal dan akhir
    url = url.replace("https://", "")
    url = url.replace("http://", "")
    url = url.replace("www.", "")
    cleaned_urls.append(url)

# Menyimpan hasil ke file url-new.txt
with open('hasil_pencarian_url.txt', 'w') as file:
    for url in cleaned_urls:
        file.write(url + '\n')