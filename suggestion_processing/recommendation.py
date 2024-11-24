import sys
from sentence_transformers import SentenceTransformer, util
import mysql.connector

# Ghi log để kiểm tra quá trình
def log(message):
    with open("debug_log.txt", "a", encoding="utf-8") as log_file:
        log_file.write(message + "\n")

# Kết nối đến cơ sở dữ liệu
try:
    conn = mysql.connector.connect(
        host="localhost",
        user="root",
        password="",
        database="enchantelle"
    )
    cursor = conn.cursor(dictionary=True)
    log("Kết nối cơ sở dữ liệu thành công.")
except Exception as e:
    log(f"Lỗi kết nối cơ sở dữ liệu: {e}")
    sys.exit(1)

# Lấy user_id từ PHP
try:
    user_id = sys.argv[1]
except IndexError:
    log("Thiếu user_id.")
    sys.exit(1)

# Lấy thông tin từ bảng user_preferences
try:
    cursor.execute("SELECT * FROM user_preferences WHERE user_id = %s", (user_id,))
    user_preferences = cursor.fetchone()

    if not user_preferences:
        log(f"Không tìm thấy dữ liệu của user_id: {user_id}.")
        sys.exit(1)
except Exception as e:
    log(f"Lỗi khi truy vấn user_preferences: {e}")
    sys.exit(1)

# Tải danh sách sản phẩm từ motasanpham
try:
    cursor.execute(""" 
        SELECT sanpham_id, chatlieu_1, chatlieu_2, color_1, color_2, 
               style_1, style_2, style_3, dip_1, dip_2, dip_3,
               season_1, season_2, season_3, season_4, old_from, old_to
        FROM motasanpham 
    """)
    products = cursor.fetchall()

    if not products:
        log("Không có sản phẩm nào trong cơ sở dữ liệu.")
        sys.exit(1)
except Exception as e:
    log(f"Lỗi khi truy vấn motasanpham: {e}")
    sys.exit(1)

# Chuẩn bị dữ liệu người dùng
try:
    model = SentenceTransformer('all-MiniLM-L6-v2')
    log("Mô hình BERT được tải thành công.")
except Exception as e:
    log(f"Lỗi khi tải mô hình BERT: {e}")
    sys.exit(1)

fields = ['chatlieu', 'color', 'style', 'dip', 'season']
# Chuẩn bị dữ liệu người dùng
user_description_parts = []

# Chỉ thêm vào các trường không bằng 0 và chuyển thành chuỗi
for field in fields:
    if user_preferences[field] != '0':
        user_description_parts.append(str(user_preferences[field]))

# Thêm tuổi vào mô tả nếu có và chuyển thành chuỗi
if user_preferences['tuoi'] != '0':
    user_description_parts.append(str(user_preferences['tuoi']))

user_description = " ".join(user_description_parts)
log(f"Mô tả người dùng: {user_description}")


# Tính độ tương đồng và gợi ý sản phẩm
try:
    user_vector = model.encode(user_description, convert_to_tensor=True)
    recommended_products = []

    for product in products:
        combined_attributes = []

        # Chỉ thêm vào các thuộc tính không bằng '0' hoặc None
        for field in ['chatlieu_1', 'chatlieu_2', 'color_1', 'color_2', 
                      'style_1', 'style_2', 'style_3', 'dip_1', 'dip_2', 'dip_3', 
                      'season_1', 'season_2', 'season_3', 'season_4']:
            value = product[field]
            if value and value != '0' and value is not None:
                combined_attributes.append(str(value))

        # Tính độ tuổi trung bình (average age) nếu có
        old_from = product['old_from']
        old_to = product['old_to']
        if old_from is not None and old_to is not None:
            try:
                average_age = (int(old_from) + int(old_to)) / 2
                combined_attributes.append(str(average_age))
            except ValueError:
                log(f"Thông tin tuổi không hợp lệ cho sản phẩm {product['sanpham_id']}.")
        
        # Thêm các thuộc tính hợp lệ vào mô tả
        combined_attributes_str = " ".join(combined_attributes)
        
        # Tính độ tương đồng nếu có thuộc tính hợp lệ
        if combined_attributes_str:  # Kiểm tra xem có thuộc tính nào để tính độ tương đồng không
            product_vector = model.encode(combined_attributes_str, convert_to_tensor=True)
            similarity = util.cos_sim(user_vector, product_vector).item()
            recommended_products.append((user_id, product['sanpham_id'], similarity))

    # Lấy 12 sản phẩm có độ tương đồng cao nhất
    recommended_products = sorted(recommended_products, key=lambda x: x[2], reverse=True)[:12]

    # Lưu vào bảng recommended_products
    cursor.execute("DELETE FROM recommended_products WHERE user_id = %s", (user_id,))
    cursor.executemany(""" 
        INSERT INTO recommended_products (user_id, sanpham_id, similarity) 
        VALUES (%s, %s, %s) 
    """, recommended_products)
    conn.commit()
    log("Đã lưu gợi ý sản phẩm vào recommended_products.")
    print("Success")
except Exception as e:
    log(f"Lỗi khi xử lý sản phẩm: {e}")
    sys.exit(1)
