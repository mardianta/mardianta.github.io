import os

# Folder yang berisi file
folder_path = 'data1'
# Folder untuk menyimpan hasil
output_folder = 'data'

# Keyword yang ingin dihapus jika ada dalam baris
keyword_to_remove = 'android://'  # Ganti sesuai kebutuhan

# Membuat folder output jika belum ada
os.makedirs(output_folder, exist_ok=True)

# Iterasi melalui semua file dalam folder
for filename in os.listdir(folder_path):
    if filename.endswith('.txt'):  # Sesuaikan ekstensi file jika perlu
        file_path = os.path.join(folder_path, filename)
        output_file_path = os.path.join(output_folder, filename)

        with open(file_path, 'r') as file:
            lines = file.readlines()

        # Filter: hapus baris yang mengandung keyword_to_remove
        filtered_lines = [line for line in lines if keyword_to_remove not in line]

        # Simpan hasil ke file baru tanpa baris yang mengandung keyword
        with open(output_file_path, 'w') as output_file:
            output_file.writelines(filtered_lines)

print(f"Proses selesai. Semua baris yang mengandung '{keyword_to_remove}' telah dihapus dan hasil disimpan di folder '{output_folder}'.")

