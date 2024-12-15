import sys
import mysql.connector
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity

# Hàm ghi log để kiểm tra quá trình xử lý
def log(message):
    with open("debug_log.txt", "a", encoding="utf-8") as log_file:
        log_file.write(message + "\n")

# Kết nối cơ sở dữ liệu
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

# Lấy user_id từ tham số dòng lệnh
try:
    user_id = sys.argv[1]
except IndexError:
    log("Thiếu user_id. Hãy truyền user_id từ PHP.")
    sys.exit(1)

# Truy vấn thông tin từ bảng user_preferences
try:
    cursor.execute("SELECT * FROM user_preferences WHERE user_id = %s", (user_id,))
    user_preferences = cursor.fetchone()

    if not user_preferences:
        log(f"Không tìm thấy dữ liệu của user_id: {user_id}.")
        sys.exit(1)
except Exception as e:
    log(f"Lỗi khi truy vấn user_preferences: {e}")
    sys.exit(1)

# Truy vấn danh sách sản phẩm từ bảng motasanpham
try:
    cursor.execute(""" 
        SELECT sanpham_id, chatlieu_1, chatlieu_2, color_1, color_2, 
               style_1, style_2, style_3, dip_1, dip_2, dip_3,
               season_1, season_2, season_3, season_4, old_from, old_to, hoatiet
        FROM motasanpham 
    """)
    products = cursor.fetchall()

    if not products:
        log("Không có sản phẩm nào trong cơ sở dữ liệu.")
        sys.exit(1)
except Exception as e:
    log(f"Lỗi khi truy vấn motasanpham: {e}")
    sys.exit(1)

# Chuẩn bị dữ liệu mô tả người dùng
try:
    user_description_parts = []
    fields = ['chatlieu', 'color', 'style', 'dip', 'season', 'hoatiet']

    # Lấy dữ liệu từ user_preferences và loại bỏ giá trị '0'
    for field in fields:
        if user_preferences[field] != '0':
            user_description_parts.append(str(user_preferences[field]))

    # Thêm thông tin tuổi nếu có
    if user_preferences['tuoi'] != '0':
        user_description_parts.append(str(user_preferences['tuoi']))

    user_description = " ".join(user_description_parts)
    log(f"Mô tả người dùng: {user_description}")
except Exception as e:
    log(f"Lỗi khi chuẩn bị dữ liệu mô tả người dùng: {e}")
    sys.exit(1)

# Tạo danh sách mô tả sản phẩm
try:
    product_descriptions = []

    for product in products:
        combined_attributes = []

        # Lấy các thuộc tính hợp lệ của sản phẩm
        for field in ['chatlieu_1', 'chatlieu_2', 'color_1', 'color_2', 
                      'style_1', 'style_2', 'style_3', 'dip_1', 'dip_2', 'dip_3',
                      'season_1', 'season_2', 'season_3', 'season_4', 'hoatiet']:
            value = product[field]
            if value and value != '0' and value is not None:
                combined_attributes.append(str(value))

        # Thêm thông tin tuổi trung bình nếu có
        old_from = product['old_from']
        old_to = product['old_to']
        if old_from is not None and old_to is not None:
            try:
                average_age = (int(old_from) + int(old_to)) / 2
                combined_attributes.append(str(average_age))
            except ValueError:
                log(f"Lỗi tính tuổi trung bình cho sản phẩm {product['sanpham_id']}.")

        # Ghép các thuộc tính thành chuỗi mô tả sản phẩm
        combined_attributes_str = " ".join(combined_attributes)
        if combined_attributes_str:
            product_descriptions.append(combined_attributes_str)

    log("Đã chuẩn bị dữ liệu mô tả sản phẩm.")
except Exception as e:
    log(f"Lỗi khi chuẩn bị dữ liệu mô tả sản phẩm: {e}")
    sys.exit(1)

# Áp dụng TF-IDF Vectorizer
try:
    # Tạo danh sách các mô tả bao gồm mô tả của người dùng và sản phẩm
    all_descriptions = [user_description] + product_descriptions

    # Tính toán TF-IDF cho tất cả mô tả
    vectorizer = TfidfVectorizer(stop_words='english')
    tfidf_matrix = vectorizer.fit_transform(all_descriptions)

    # Tính toán độ tương đồng cosine giữa người dùng và sản phẩm
    user_vector = tfidf_matrix[0]  # Vector của người dùng
    product_vectors = tfidf_matrix[1:]  # Vectors của sản phẩm

    similarities = cosine_similarity(user_vector, product_vectors).flatten()

    # Lưu kết quả gợi ý sản phẩm
    recommended_products = [(user_id, products[i]['sanpham_id'], float(similarities[i])) for i in range(len(products))]

    # Lấy 12 sản phẩm có độ tương đồng cao nhất
    recommended_products = sorted(recommended_products, key=lambda x: x[2], reverse=True)[:12]

    # Ghi nội dung chi tiết của 12 sản phẩm có độ tương đồng cao nhất vào log
    log("12 sản phẩm có độ tương đồng cao nhất:")

    for product in recommended_products:
        user_id, sanpham_id, similarity = product

        # Truy vấn thông tin chi tiết của sản phẩm từ bảng motasanpham
        cursor.execute("""
            SELECT sanpham_id, chatlieu_1, chatlieu_2, color_1, color_2, 
                style_1, style_2, style_3, dip_1, dip_2, dip_3,
                season_1, season_2, season_3, season_4, old_from, old_to, hoatiet
            FROM motasanpham 
            WHERE sanpham_id = %s
        """, (sanpham_id,))
        product_details = cursor.fetchone()

        if product_details:
            # Ghi chi tiết sản phẩm vào log
            log(f"User ID: {user_id}, Product ID: {sanpham_id}, Similarity: {similarity}")
            for field, value in product_details.items():
                log(f"{field}: {value}")
        else:
            log(f"Không tìm thấy chi tiết sản phẩm với ID: {sanpham_id}")

    # Xóa các sản phẩm cũ trong bảng recommended_products của user_id
    cursor.execute("DELETE FROM recommended_products WHERE user_id = %s", (user_id,))

    # Lưu kết quả vào cơ sở dữ liệu
    cursor.executemany("""
        INSERT INTO recommended_products (user_id, sanpham_id, similarity)
        VALUES (%s, %s, %s)
    """, recommended_products)
    conn.commit()

    log("Đã lưu gợi ý sản phẩm vào bảng recommended_products.")
    print("Success")

except Exception as e:
    log(f"Lỗi khi tính toán TF-IDF và lưu kết quả: {e}")
    sys.exit(1)
