export default class Util {

    static formatarNumerico(valor: Number){
        return valor.toLocaleString('pt-BR', {
                style: 'currency',
                currency: 'BRL',
                minimumFractionDigits: 2
            });
    }
}