import os

def split_file(file_path, chunk_size_mb=5):
    """
    Membagi file teks menjadi beberapa bagian dengan ukuran tertentu.

    Args:
        file_path (str): Path ke file input.
        chunk_size_mb (int): Ukuran maksimal setiap bagian dalam MB.
    """
    if not os.path.exists(file_path):
        print(f"Error: File '{file_path}' tidak ditemukan.")
        return

    if not os.path.isfile(file_path):
        print(f"Error: '{file_path}' bukanlah sebuah file.")
        return

    chunk_size_bytes = chunk_size_mb * 1024 * 1024  # Konversi MB ke bytes
    file_number = 1
    base_name, ext = os.path.splitext(os.path.basename(file_path))
    output_dir = os.path.dirname(file_path) or '.'  # Gunakan direktori file input atau direktori saat ini

    try:
        with open(file_path, 'r', encoding='utf-8') as f_in:
            current_chunk_size = 0
            output_file = None
            output_file_path = ""

            for line in f_in:
                if output_file is None:
                    output_file_path = os.path.join(output_dir, f"{base_name}_part{file_number}{ext}")
                    output_file = open(output_file_path, 'w', encoding='utf-8')
                    print(f"Membuat file: {output_file_path}")
                    current_chunk_size = 0

                output_file.write(line)
                current_chunk_size += len(line.encode('utf-8'))  # Hitung ukuran dalam bytes

                # Jika ukuran chunk sudah mencapai batas, tutup file dan mulai file baru
                if current_chunk_size >= chunk_size_bytes:
                    output_file.close()
                    output_file = None
                    file_number += 1
            
            if output_file:  # Pastikan file terakhir ditutup jika masih terbuka
                output_file.close()
        
        print(f"Proses pemisahan file '{file_path}' selesai.")

    except Exception as e:
        print(f"Terjadi kesalahan: {e}")
        if output_file and not output_file.closed:
            output_file.close()


if __name__ == "__main__":
    input_file = "data/data_com_juni_17.txt"  # Ganti dengan nama file Anda

    # Jalankan fungsi split_file pada file yang diminta
    split_file(input_file, chunk_size_mb=5)
