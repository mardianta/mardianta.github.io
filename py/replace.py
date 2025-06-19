import os

# Folder yang berisi file
folder_path = 'data1'
# Folder untuk menyimpan hasil
output_folder = 'data'

# Keyword lama dan baru
old_keyword = ' == .com'
new_keyword = ''  # Ganti sesuai kebutuhan

# Membuat folder output jika belum ada
os.makedirs(output_folder, exist_ok=True)

# Iterasi melalui semua file dalam folder
for filename in os.listdir(folder_path):
    if filename.endswith('.txt'):  # Ganti dengan ekstensi file yang sesuai
        file_path = os.path.join(folder_path, filename)
        output_file_path = os.path.join(output_folder, filename)

        with open(file_path, 'r') as file:
            content = file.read()

        # Ganti keyword lama dengan baru
        new_content = content.replace(old_keyword, new_keyword)

        # Simpan hasil ke file baru
        with open(output_file_path, 'w') as output_file:
            output_file.write(new_content)

print(f"Proses selesai. Semua '{old_keyword}' telah diganti dengan '{new_keyword}' di folder '{output_folder}'.")

