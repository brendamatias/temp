import React, { useEffect, useState } from 'react';
import ReactDOM from 'react-dom/client';
import './index.css';
import App from './App';
import reportWebVitals from './reportWebVitals';
import { createBrowserRouter, RouterProvider, useParams } from 'react-router-dom';
import ProdutoPage from './components/pages/produtos';
import ProdutoForm from './components/pages/produtos/form';
import CategoriaList from './components/pages/categorias';
import CategoriaForm from './components/pages/categorias/form';
import ProdutoDetalhes from './components/pages/produtos/details';
import Home from './components/pages/Home';
import ErrorPage from './components/pages/Error-page';



const router = createBrowserRouter([
  {
    path: "/",
    element: <App />,
    errorElement: <ErrorPage/>,
    children: [
      {
        path: "/",
        element: <Home />
      },
      {
        path: "/listar-produtos",
        element: <ProdutoPage />
      },
      {
        path: "/cadastrar-produto",
        element: <ProdutoForm />
      },
      {
        path: "/listar-categorias",
        element: <CategoriaList />
      },
      {
        path: "/cadastrar-categoria",
        element: <CategoriaForm />
      },
      {
        path: `/buscar-produto/:id`,
        element: <ProdutoDetalhes />
      }
    ]
  },

]);

const root = ReactDOM.createRoot(
  document.getElementById('root') as HTMLElement
);  
root.render(
  <React.StrictMode>
    <RouterProvider router={router} />
  </React.StrictMode>
);

reportWebVitals();
