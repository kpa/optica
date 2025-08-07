import { useState } from 'react'
import opticaLogo from './assets/optica-arena-logo.jpg'
import './App.css'

function App() {
  const [count, setCount] = useState(0)

  return (
    <>
      <div>
        <a href="http://localhost:8000/index.php" target="_blank">
          <img src={opticaLogo} className="logo" alt="Arena logo" />
        </a>
      </div>
      <p>Av. Corrientes 987</p>
      <p>+54 9 11 5579 7561</p>

    </>
  )
}

export default App
