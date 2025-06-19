import os
import re

def load_patterns(pattern_file):
    """Load regex patterns from a file."""
    with open(pattern_file, 'r') as f:
        patterns = [line.strip() for line in f if line.strip()]
    return patterns

def search_files(directory, patterns, output_file):
    """Search for files containing any of the regex patterns and save results to an output file."""
    # Compile the patterns into a single regex
    combined_pattern = re.compile('|'.join(patterns))
    
    # Open the output file for writing
    with open(output_file, 'w') as out_file:
        # Walk through the directory
        for root, dirs, files in os.walk(directory):
            for file in files:
                file_path = os.path.join(root, file)
                try:
                    with open(file_path, 'r', encoding='utf-8') as f:
                        for line_number, line in enumerate(f, start=1):
                            matches = combined_pattern.findall(line)
                            if matches:
                                for match in matches:
                                    result = f"{line.strip()} == {match}\n"
                                    print(result.strip())  # Print to console
                                    out_file.write(result)  # Write to output file
                except (UnicodeDecodeError, FileNotFoundError):
                    # Skip files that can't be read or don't exist
                    continue

if __name__ == "__main__":
    # Specify the path to your pattern file and the directory to search
    pattern_file = 'list.txt'  # Ganti dengan path ke file list.txt Anda
    search_directory = '/Users/dsi1/Desktop/Breach Forums/Original File/TXTlog/innereal'  # Ganti dengan path ke direktori yang ingin dicari
    output_file = 'hasil_pencarian.txt'  # Nama file output

    # Load patterns and search files
    patterns = load_patterns(pattern_file)
    search_files(search_directory, patterns, output_file)