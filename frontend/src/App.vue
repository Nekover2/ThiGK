<template>
  <div class="calculator-container">
    <div class="display">
      <div class="history">{{ historyDisplay }}</div>
      <div class="current">{{ currentDisplay }}</div>
    </div>

    <div class="buttons">
      <button class="btn op modify-btn" @click="setOperation(4, '/')">/</button>
      <button class="btn op modify-btn" @click="setOperation(3, '*')">*</button>
      <button class="btn op modify-btn" @click="setOperation(2, '-')">-</button>
      <button class="btn op modify-btn" @click="setOperation(1, '+')">+</button>

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
      <button class="btn zero-button" @click="appendNumber(0)">0</button>
      <button class="btn" @click="appendDot">.</button>
      <button class="btn empty-btn"></button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const currentDisplay = ref('0');
const number1 = ref(null);
const action = ref(null);
const operatorSymbol = ref('');
const isNewInput = ref(true);
const historyDisplay = ref('');

// Hàm nhập số
const appendNumber = (num) => {
  if (isNewInput.value) {
    currentDisplay.value = num.toString();
    isNewInput.value = false;
  } else {
    // Nếu số đang là 0 và nhập số khác 0 thì thay thế (tránh 05, 06)
    if (currentDisplay.value === '0') {
      currentDisplay.value = num.toString();
    } else {
      currentDisplay.value += num.toString();
    }
  }
};

// Hàm nhập dấu chấm
const appendDot = () => {
  if (isNewInput.value) {
    currentDisplay.value = '0.';
    isNewInput.value = false;
    return;
  }
  // Chỉ thêm dấu chấm nếu chưa có
  if (!currentDisplay.value.includes('.')) {
    currentDisplay.value += '.';
  }
};

const setOperation = (act, symbol) => {
  number1.value = parseFloat(currentDisplay.value);
  action.value = act;
  operatorSymbol.value = symbol;
  isNewInput.value = true;
  historyDisplay.value = `${number1.value} ${symbol}`;
};

const calculateResult = async () => {
  const number2 = parseFloat(currentDisplay.value);
  
  if (number1.value === null || action.value === null) return;

  historyDisplay.value = `${number1.value} ${operatorSymbol.value} ${number2} =`;

  const payload = {
    action: action.value,
    number1: number1.value,
    number2: number2
  };

  try {
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
      // alert(data.error); // Có thể bật lại nếu muốn hiện popup
    } else {
      currentDisplay.value = data.result.toString();
      isNewInput.value = true;
    }
  } catch (error) {
    console.error("Error:", error);
    currentDisplay.value = "Err";
  }
};
</script>

<style scoped>
.calculator-container {
  width: 320px;
  background-color: #202124;
  margin: 50px auto;
  border-radius: 10px;
  overflow: hidden;
  font-family: Arial, sans-serif;
  box-shadow: 0px 4px 10px rgba(0,0,0,0.3);
}

.display {
  background-color: #202124;
  color: white;
  text-align: right;
  padding: 20px;
  height: 150px;
  display: flex;
  flex-direction: column;
  justify-content: flex-end;
}

.history {
  font-size: 18px;
  color: #9aa0a6;
  margin-bottom: 5px;
  min-height: 24px;
}

.current {
  font-size: 48px;
  word-wrap: break-word;
}
.modify-btn {
  background-color: #2d4d7c !important;
  color: white !important;
}

.buttons {
  display: grid;
  grid-template-columns: repeat(4, 1fr); /* Grid 4 cột đều nhau */
  background-color: #3a3a3a;
  gap: 1px; /* Tạo khoảng cách nhỏ giữa các nút */
  border-top: 1px solid #5f6368;
  margin: 1px;
}

.zero-button {
  grid-column: span 2; /* Nút 0 chiếm 2 cột */
}
.empty-btn {
  background-color: #1e1e1e !important;
  border: none;
  cursor: default;
  grid-column: span 3;
}
.btn {
  background-color: #3a3a3a;
  border: none;
  color: white;
  font-size: 24px;
  height: 80px;
  cursor: pointer;
  transition: background 0.2s;
}

.btn:hover {
  background-color: #5f6368;
}

.btn:active {
  background-color: #70757a;
}

/* Nút phép tính (Hàng 1) */
.btn.op {
  background-color: #2d4d7c;
  color: white;
}

/* Nút Bằng (=) màu xanh dương */
.btn.eq {
  background-color: #4285f4;
  color: white;
}
.btn.eq:hover {
  background-color: #5294fa;
}
</style>