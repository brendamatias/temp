import { AppBar, Avatar, Box, IconButton, Menu, MenuItem, Toolbar, Tooltip, Typography, Container, Button, Fade } from "@mui/material";
import AdbIcon from '@mui/icons-material/Adb';
import React, { useState } from "react";
import { Link } from 'react-router-dom';

const pages = [
    {
        id: 1,
        name: 'Produto',
        path: '/',
        children: [
            {
                name: 'Cadastrar',
                path: '/cadastrar-produto'
            },
            {
                name: 'À venda',
                path: '/listar-produtos'
            },
            {
                name: 'Pesquisar',
                path: '/pesquisar-produto'
            }
        ]
    },
    {
        id: 2,
        name: 'Categorias',
        path: '/',
        children: [
            {
                name: 'Listar',
                path: '/listar-categorias'
            },
            {
                name: 'Cadastrar',
                path: '/cadastrar-categoria'
            },
        ]
    }

];

const settings = ['Profile', 'Account', 'Dashboard', 'Logout'];

export default function MenuBar() {

    const [, setAnchorElNav] = React.useState<null | HTMLElement>(null);
    const [anchorElUser, setAnchorElUser] = React.useState<null | HTMLElement>(null);
    const [anchorEl, setAnchorEl] = useState<{ [key: number]: HTMLElement | null }>({});


    const handleClickMenu = (event: React.MouseEvent<HTMLElement>, id: number) => {
        setAnchorEl((prev) => ({ ...prev, [id]: event.currentTarget }));
    };
    const handleCloseMenu = (id: number) => {
        setAnchorEl((prev) => ({ ...prev, [id]: null }));
    };

    const handleOpenNavMenu = (event: React.MouseEvent<HTMLElement>) => {
        setAnchorElNav(event.currentTarget);
    };
    const handleOpenUserMenu = (event: React.MouseEvent<HTMLElement>) => {
        setAnchorElUser(event.currentTarget);
    };

    const handleCloseUserMenu = () => {
        setAnchorElUser(null);
    };


    return (
        <>
            <AppBar position="static" style={{backgroundColor :'#323946'}}>
                <Container maxWidth="xl">
                    <Toolbar disableGutters>
                        <AdbIcon sx={{ display: { xs: 'none', md: 'flex' }, mr: 1 }} />
                        <Typography
                            variant="h6"
                            noWrap
                            component="a"
                            href="/"
                            sx={{
                                mr: 2,
                                display: { xs: 'none', md: 'flex' },
                                fontFamily: 'monospace',
                                fontWeight: 700,
                                letterSpacing: '.3rem',
                                color: 'inherit',
                                textDecoration: 'none',
                            }}
                        >
                            VIBBRANEO
                        </Typography>

                        <Box sx={{ flexGrow: 1, display: { xs: 'flex', md: 'none' } }}>
                            <IconButton
                                size="large"
                                aria-label="account of current user"
                                aria-controls="menu-appbar"
                                aria-haspopup="true"
                                onClick={handleOpenNavMenu}
                                color="inherit"
                            >
                            </IconButton>
                        </Box>

                        <AdbIcon sx={{ display: { xs: 'flex', md: 'none' }, mr: 1 }} />
                        <Typography
                            variant="h5"
                            noWrap
                            component="a"
                            href="/"
                            sx={{
                                mr: 2,
                                display: { xs: 'flex', md: 'none' },
                                flexGrow: 1,
                                fontFamily: 'monospace',
                                fontWeight: 700,
                                letterSpacing: '.3rem',
                                color: 'inherit',
                                textDecoration: 'none',
                            }}
                        >
                            LOGO
                        </Typography>

                        <Box sx={{ flexGrow: 1, display: { xs: 'none', md: 'flex' } }}>
                            {pages.map((page) => (
                                <div key={page.id}>
                                    <Button
                                        id={`fade-button-${page.id}`}
                                        aria-controls={anchorEl[page.id] ? 'fade-menu' : undefined}
                                        aria-haspopup="true"
                                        aria-expanded={anchorEl[page.id] ? 'true' : undefined}
                                        onClick={(event) => handleClickMenu(event, page.id)}
                                        style={{ color: 'white' }}
                                    >
                                        {page.name}
                                    </Button>

                                    <Menu
                                        key={page.path}
                                        id={`fade-menu-${page.id}`}
                                        anchorEl={anchorEl[page.id]}
                                        open={Boolean(anchorEl[page.id])}
                                        onClose={() => handleCloseMenu(page.id)}
                                        TransitionComponent={Fade}
                                        MenuListProps={{
                                            'aria-labelledby': `fade-button-${page.id}`,
                                        }}
                                    >
                                        {page.children.map((item) => (
                                            <MenuItem key={item.name} onClick={() => handleCloseMenu(page.id)}>
                                                <Link to={item.path} style={{ textDecoration: 'none', color: 'inherit' }}>
                                                    {item.name}
                                                </Link>
                                            </MenuItem>
                                        ))}
                                    </Menu>
                                </div>
                            ))}
                        </Box>

                        <Box sx={{ flexGrow: 0 }}>
                            <Tooltip title="Open settings">
                                <IconButton onClick={handleOpenUserMenu} sx={{ p: 0 }}>
                                    <Avatar alt="Remy Sharp" src="/static/images/avatar/2.jpg" />
                                </IconButton>
                            </Tooltip>
                            <Menu
                                sx={{ mt: '45px' }}
                                id="menu-appbar"
                                anchorEl={anchorElUser}
                                anchorOrigin={{
                                    vertical: 'top',
                                    horizontal: 'right',
                                }}
                                keepMounted
                                transformOrigin={{
                                    vertical: 'top',
                                    horizontal: 'right',
                                }}
                                open={Boolean(anchorElUser)}
                                onClose={handleCloseUserMenu}
                            >
                                {settings.map((setting) => (
                                    <MenuItem key={setting} onClick={handleCloseUserMenu}>
                                        <Typography sx={{ textAlign: 'center' }}>{setting}</Typography>
                                    </MenuItem>
                                ))}
                            </Menu>
                        </Box>
                    </Toolbar>
                </Container>
            </AppBar>
        </>
    );
}