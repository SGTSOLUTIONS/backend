import os
import pandas as pd

# ✅ Your folder path
folder_path = r"C:\Users\SGTSOLUTIONS\OneDrive\Documents\area variation\central"

# 🔁 Loop through all Excel files in the folder
for filename in os.listdir(folder_path):
    if filename.endswith(".xlsx") or filename.endswith(".xls"):
        file_path = os.path.join(folder_path, filename)
        print(f"📂 Reading: {file_path}")

        try:
            df = pd.read_excel(file_path, engine='openpyxl')

            # Find column with "X- Area" and "Variation" in its name (even with \n)
            target_col = None
            for col in df.columns:
                if "X- Area" in col and "Variation" in col:
                    target_col = col
                    break

            if not target_col:
                print(f"❌ 'X- Area Variations' column not found in {filename}")
                continue

            def categorize(value):
                try:
                    if pd.isna(value) or str(value).strip().upper() == "N/A":
                        return "No MIS Area"
                    x = float(str(value).replace("x", "").strip())
                    if x <= 0.5: return "0.5"
                    elif x <= 1.0: return "1.0"
                    elif x <= 1.5: return "1.5"
                    elif x <= 2.0: return "2.0"
                    elif x <= 2.5: return "2.5"
                    elif x <= 3.0: return "3.0"
                    elif x <= 3.5: return "3.5"
                    elif x <= 4.0: return "4.0"
                    else: return "> 4.0"
                except Exception as e:
                    return "Invalid"

            # ➕ Add the new category column
            df["X-Variation Category"] = df[target_col].apply(categorize)

            # 💾 Save updated file
            output_path = os.path.join(folder_path, f"updated_{filename}")
            df.to_excel(output_path, index=False, engine='openpyxl')
            print(f"✅ Updated and saved: {output_path}\n")

        except Exception as e:
            print(f"❌ Failed to process {filename}: {e}")
