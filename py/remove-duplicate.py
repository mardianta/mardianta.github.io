def remove_duplicates(input_file, output_file):
    """Remove duplicate lines from a text file."""
    seen = set()  # Set untuk menyimpan baris yang sudah dilihat
    with open(input_file, 'r', encoding='utf-8') as infile:
        with open(output_file, 'w', encoding='utf-8') as outfile:
            for line in infile:
                # Jika baris belum ada di set, tambahkan ke set dan tulis ke file output
                if line not in seen:
                    seen.add(line)
                    outfile.write(line)

if __name__ == "__main__":
    # Ganti dengan path ke file input dan output Anda
    input_file = 'hasil_pencarian.txt'  # Nama file input
    output_file = 'hasil_pencarian1.txt'  # Nama file output

    remove_duplicates(input_file, output_file)
    print(f"Duplicate lines removed. Results saved to {output_file}.")
