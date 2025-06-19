import os

# Folder yang berisi file
folder_path = 'data'
# Folder untuk menyimpan hasil
output_folder = 'data1'

# Membuat folder output jika belum ada
os.makedirs(output_folder, exist_ok=True)

# Iterasi melalui semua file dalam folder
for filename in os.listdir(folder_path):
    if filename.endswith('.txt'):  # Ganti dengan ekstensi file yang sesuai
        file_path = os.path.join(folder_path, filename)
        output_file_path = os.path.join(output_folder, filename)

        with open(file_path, 'r') as file:
            lines = file.readlines()

        # Menyimpan baris yang mengandung '=id'
        with open(output_file_path, 'w') as output_file:
            for line in lines:
                if '== .com' in line:
                    output_file.write(line)

print("Proses selesai. Baris yang mengandung '=id' telah disimpan di folder 'output'.")
