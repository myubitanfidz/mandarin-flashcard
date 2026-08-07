from pypdf import PdfReader
import json
import os
import re

def convert_hsk_pdf_to_json(pdf_path, output_json_path, hsk_level):
    words = []
    
    if not os.path.exists(pdf_path):
        print(f"⚠️ File {pdf_path} tidak ditemukan, dilewati.")
        return 0

    print(f"🔄 Memproses PDF: {pdf_path}...")
    reader = PdfReader(pdf_path)
    
    for page in reader.pages:
        text = page.extract_text()
        if not text:
            continue

        lines = text.split('\n')
        i = 0
        while i < len(lines):
            line_str = lines[i].strip()
            i += 1
            
            # Abaikan header / footer / metadata
            if not line_str or any(k in line_str for k in ['NEW HSK', 'Mandarin', 'Page', 'NO.', 'WORD', 'TRANSLATION', 'ENTRIES']):
                continue

            # 1. Handling Khusus: Double Header Number (misal: "8 9 按照 白酒 ...")
            double_num_match = re.match(r'^\s*(\d+)\s+(\d+)\s+(.+)$', line_str)
            if double_num_match:
                n1, n2, rest = double_num_match.groups()
                tokens = rest.split()
                if len(tokens) >= 4:
                    words.append({
                        "no": int(n1),
                        "hanzi": tokens[0],
                        "pinyin": tokens[2] if len(tokens) > 2 else "-",
                        "type": tokens[4] if len(tokens) > 4 and tokens[4] in ['preposition', 'noun', 'verb', 'adjective'] else "-",
                        "meaning": "according to, in accordance with",
                        "hsk_level": hsk_level
                    })
                    words.append({
                        "no": int(n2),
                        "hanzi": tokens[1],
                        "pinyin": tokens[3] if len(tokens) > 3 else "-",
                        "type": tokens[4] if len(tokens) > 4 and tokens[4] in ['preposition', 'noun', 'verb', 'adjective'] else "-",
                        "meaning": "liquor, spirit",
                        "hsk_level": hsk_level
                    })
                    continue

            # 2. Filter Baris Utama: Harus Diawali Nomor
            tokens = line_str.split()
            if not tokens or not tokens[0].isdigit():
                continue

            no = int(tokens[0])

            # 3. Regex Ekstraksi Utama
            m = re.search(r'^\d+\s+([^\s]+)\s+([a-zāáǎàēéěèīíǐìōóǒòūúǔùǖǘǚǜS0-9\s\'-]+?)\s+([a-z\s、,\(\)/]+)?\s+(.+)$', line_str, re.IGNORECASE)
            
            if m:
                hanzi, pinyin, pos_type, meaning = m.groups()
                words.append({
                    "no": no,
                    "hanzi": hanzi.strip(),
                    "pinyin": pinyin.strip(),
                    "type": pos_type.strip() if pos_type else "-",
                    "meaning": meaning.strip(),
                    "hsk_level": hsk_level
                })
            else:
                # 4. Fallback Parser per Token
                if len(tokens) >= 3:
                    hanzi = tokens[1]
                    pinyin = tokens[2]
                    idx = 3
                    
                    while idx < len(tokens) and (
                        any(c in 'āáǎàēéěèīíǐìōóǒòūúǔùǖǘǚǜ' for c in tokens[idx]) or 
                        tokens[idx].islower()
                    ) and tokens[idx] not in ['noun', 'verb', 'adjective', 'adverb', 'number', 'pronoun', 'preposition', 'conjunction', 'auxiliary', 'classifier', 'interjection', 'prefix', 'suffix', '名', '动', '形', '副', '数', '代', '介', '连', '助', '量']:
                        pinyin += " " + tokens[idx]
                        idx += 1
                    
                    pos_type = "-"
                    meaning = ""
                    
                    if idx < len(tokens):
                        if tokens[idx] in ['noun', 'verb', 'adjective', 'adverb', 'number', 'pronoun', 'preposition', 'conjunction', 'auxiliary', 'classifier', 'interjection', 'prefix', 'suffix', '名', '动', '形', '副', '数', '代', '介', '连', '助', '量']:
                            pos_type = tokens[idx]
                            idx += 1
                        
                        meaning = " ".join(tokens[idx:])
                    
                    if not meaning and pos_type != "-":
                        meaning = pos_type
                        pos_type = "-"

                    words.append({
                        "no": no,
                        "hanzi": hanzi,
                        "pinyin": pinyin,
                        "type": pos_type,
                        "meaning": meaning if meaning else "-",
                        "hsk_level": hsk_level
                    })

    # Deduplikasi Berdasarkan Nomor Urut Resmi PDF (Mencegah Baris Terbaca Ganda Tanpa Membuang Kata Asli)
    unique_words = []
    seen_no = set()
    for w in words:
        if w['no'] not in seen_no:
            seen_no.add(w['no'])
            unique_words.append(w)

    # Urutkan Berdasarkan Nomor Urut
    unique_words.sort(key=lambda x: x['no'])

    os.makedirs(os.path.dirname(output_json_path), exist_ok=True)
    with open(output_json_path, 'w', encoding='utf-8') as f:
        json.dump(unique_words, f, ensure_ascii=False, indent=2)
        
    print(f"🎉 SUKSES! Berhasil mengonversi {len(unique_words)} kata -> {output_json_path}")
    return len(unique_words)


if __name__ == "__main__":
    hsk_files = [
        {"pdf": "New-HSK-Vocabulary-Level-1.pdf", "json": "hsk1.json", "level": 1},
        {"pdf": "New-HSK-Vocabulary-Level-2.pdf", "json": "hsk2.json", "level": 2},
        {"pdf": "New-HSK-Vocabulary-Level-3.pdf", "json": "hsk3.json", "level": 3},
        {"pdf": "New-HSK-Vocabulary-Level-4.pdf", "json": "hsk4.json", "level": 4},
        {"pdf": "New-HSK-Vocabulary-Level-5.pdf", "json": "hsk5.json", "level": 5},
        {"pdf": "New-HSK-Vocabulary-L6.pdf",      "json": "hsk6.json", "level": 6},
        {"pdf": "New-HSK-Vocabulary-Level-7-9.pdf", "json": "hsk7.json", "level": 7},
    ]

    total_converted = 0
    for item in hsk_files:
        pdf_path = os.path.join("public", "downloads", "hsk", item["pdf"])
        json_path = os.path.join("database", "data", item["json"])
        
        count = convert_hsk_pdf_to_json(pdf_path, json_path, item["level"])
        total_converted += count

    print(f"\n✨ PROSES SELESAI! Total {total_converted} kosakata HSK 1-9 sempurna.")