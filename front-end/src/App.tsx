import './App.css';
import MenuBar from './components/menus/MenuBar';
import { Outlet } from 'react-router-dom';
import Footer from './components/menus/FooterBar';

function App() {
  return (
    <>
    <div className="App">
        <MenuBar />
          <Outlet />
        <Footer/>
    </div>
    </>
  );
}

export default App;
