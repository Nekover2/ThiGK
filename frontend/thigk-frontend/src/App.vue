<template>
  <div class="calculator-container">
    <div class="display">
      <div class="history">{{ historyDisplay }}</div>
      <div class="current">{{ currentDisplay }}</div>
    </div>

    <div class="buttons">
      <button class="btn op" @click="setOperation(4, '/')">/</button>
      <button class="btn op" @click="setOperation(3, '*')">*</button>
      <button class="btn op" @click="setOperation(2, '-')">-</button>
      <button class="btn op" @click="setOperation(1, '+')">+</button>

      <button class="btn" @click="appendNumber(7)">7</button>
      <button class="btn" @click="appendNumber(8)">8</button>
      <button class="btn" @click="appendNumber(9)">9</button>
      <button class="btn eq" @click="calculateResult">=</button>

      <button class="btn" @click="appendNumber(4)">4</button>
      <button class="btn" @click="appendNumber(5)">5</button>
      <button class="btn" @click="appendNumber(6)">6</button>
      <button class="btn" @click="appendNumber(1)">1</button>

      <button class="btn" @click="appendNumber(2)">2</button>
      <button class="btn" @click="appendNumber(3)">3</button>
      <button class="btn zero" @click="appendNumber(0)">0</button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const currentDisplay = ref('0'); // Số hiện tại trên màn hình
const number1 = ref(null);       // Số hạng thứ nhất
const action = ref(null);        // Mã hành động (1,2,3,4)
const operatorSymbol = ref('');  // Ký tự phép tính (+,-,*,/)
const isNewInput = ref(true);    // Cờ để reset màn hình khi nhập số mới
const historyDisplay = ref('');  // Hiển thị dòng trên (VD: 1 + 5 =)

// Hàm nhập số
const appendNumber = (num) => {
  if (isNewInput.value) {
    currentDisplay.value = num.toString();
    isNewInput.value = false;
  } else {
    currentDisplay.value += num.toString();
  }
};

// Hàm chọn phép tính
const setOperation = (act, symbol) => {
  number1.value = parseFloat(currentDisplay.value);
  action.value = act;
  operatorSymbol.value = symbol;
  isNewInput.value = true;
  historyDisplay.value = `${number1.value} ${symbol}`;
};

// Hàm tính toán (Gửi request sang PHP) 
const calculateResult = async () => {
  const number2 = parseFloat(currentDisplay.value);
  
  if (number1.value === null || action.value === null) return;

  historyDisplay.value = `${number1.value} ${operatorSymbol.value} ${number2} =`;

  // Payload gửi đi 
  const payload = {
    action: action.value,
    number1: number1.value,
    number2: number2
  };

  try {
    // Gọi API 
    const response = await fetch('http://localhost/ThiGK/api/calculator.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(payload)
    });

    const data = await response.json();

    if (data.error) {
      currentDisplay.value = "Error";
      alert(data.error);
    } else {
      currentDisplay.value = data.result; // Hiển thị kết quả JSON trả về
      isNewInput.value = true; // Reset để nhập phép tính mới
    }
  } catch (error) {
    console.error("Lỗi kết nối:", error);
    currentDisplay.value = "Err";
  }
};
</script>

<style scoped>
/* CSS cơ bản để giống hình mẫu  */
.calculator-container {
  width: 320px;
  background-color: #202124;
  margin: 50px auto;
  border-radius: 10px;
  overflow: hidden;
  font-family: Arial, sans-serif;
}

.display {
  background-color: #202124;
  color: white;
  text-align: right;
  padding: 20px;
  height: 100px;
  display: flex;
  flex-direction: column;
  justify-content: flex-end;
}

.history {
  font-size: 18px;
  color: #9aa0a6;
}

.current {
  font-size: 48px;
}

.buttons {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  background-color: #3c4043;
}

.btn {
  background-color: #3c4043;
  border: 1px solid #202124;
  color: white;
  font-size: 24px;
  height: 80px;
  cursor: pointer;
  outline: none;
}

.btn:hover {
  background-color: #5f6368;
}

/* Màu xanh cho nút phép tính */
.btn.op {
  background-color: #303134;
  color: #8ab4f8; 
  /* Trong hình đề bài thì nền xanh dương đậm, chữ trắng, bạn có thể chỉnh lại: */
  background-color: #2d3e50; 
  color: white;
}

/* Nút Bằng màu xanh dương sáng */
.btn.eq {
  background-color: #4285f4;
  color: white;
  grid-column: 4;
  grid-row: 2 / 4; /* Kéo dài 2 hàng như một số máy tính hoặc giữ nguyên 1 ô tùy layout */
  /* Theo hình đề bài, nút = nằm ở vị trí hàng 2 cột 4, nhưng layout Grid 4x4 chuẩn số 0 thường to */
}

/* Điều chỉnh lại Grid cho giống hình đề bài nhất  */
/* Hình: 
   Hàng 1: / * - +
   Hàng 2: 7 8 9 =
   Hàng 3: 4 5 6 1 (số 1 nằm đây hơi lạ, nhưng code theo logic thông thường)
   Hàng 4: 2 3 0 (số 0 to)
*/
/* Code CSS trên đang dùng Grid 4 cột chuẩn. */
</style>