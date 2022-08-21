const selectFast2Pay = async (e)=>{
    let active = document.querySelector('#fast2pay_input .list-bank a.active')
    let symbol = e.getAttribute('data-symbol')
    let bank = document.querySelector('#fast2pay_input [name="bank"]')
    if(active)active.classList.remove('active')
    e.classList.add('active')
    bank.value = symbol
}
const delay = async (delayInms=3000)=> {
    return new Promise(resolve => {
      setTimeout(() => {
        resolve(2);
      }, delayInms);
    });
  }