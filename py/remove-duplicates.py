import os

def remove_duplicates(input_file_path, output_file_path=None):
    """
    Menghapus baris duplikat dari file teks dan menyimpan hasilnya ke file baru.

    Args:
        input_file_path (str): Path file input yang berisi duplikat.
        output_file_path (str): Path file output untuk menyimpan hasil tanpa duplikat.
                                Jika None, hasil akan disimpan dengan suffix '_nodups' di nama file input.
    """
    if not os.path.exists(input_file_path):
        print(f"Error: File '{input_file_path}' tidak ditemukan.")
        return

    if not os.path.isfile(input_file_path):
        print(f"Error: '{input_file_path}' bukan sebuah file.")
        return

    if output_file_path is None:
        base, ext = os.path.splitext(input_file_path)
        output_file_path = f"{base}_nodups{ext}"

    seen_lines = set()
    try:
        with open(input_file_path, 'r', encoding='utf-8') as infile, \
             open(output_file_path, 'w', encoding='utf-8') as outfile:
            for line in infile:
                if line not in seen_lines:
                    outfile.write(line)
                    seen_lines.add(line)

        print(f"File tanpa duplikat berhasil dibuat: {output_file_path}")

    except Exception as e:
        print(f"Terjadi kesalahan saat menghapus duplikat: {e}")


if __name__ == "__main__":
    # Ganti 'file_besar.txt' dengan path file yang ingin dihapus duplikatnya
    input_file = "data1/data_com_juni_17.txt"  # File input
    # Opsional: tentukan file output jika ingin
    output_file = None  # Contoh: "file_besar_nodups.txt"

    remove_duplicates(input_file, output_file)

