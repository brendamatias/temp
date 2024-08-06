import React from 'react';
import { Dialog, DialogTitle, DialogContent, DialogActions, Button, Typography } from '@mui/material';

export default function  DialogoConfirmacao({ open, mensagem, onClose }: any){
    return (
        <Dialog open={open} onClose={onClose}>
            <DialogTitle>Confirmação</DialogTitle>
            <DialogContent>
                <Typography variant="body1" color="textPrimary">
                    {mensagem}
                </Typography>
            </DialogContent>
            <DialogActions>
                <Button onClick={onClose} color="primary">
                    Fechar
                </Button>
            </DialogActions>
        </Dialog>
    );
};