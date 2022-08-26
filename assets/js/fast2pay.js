const selectFast2Pay = async (e,dom="#fast2pay_input", name="bank")=>{
    let active = document.querySelector(dom+' .list-bank a.active')
    let symbol = e.getAttribute('data-symbol')
    let bank = document.querySelector( dom+' [name="'+name+'"]')
    let paymentType = document.querySelector( dom+' [name="payment_type"]')
    if(active)active.classList.remove('active')
    e.classList.add('active')
    bank.value = symbol
    if(paymentType)paymentType.value = e.getAttribute('data-type')
}
const alertSuccess = async ()=>{
  await delay();
  Swal.fire({
    position: 'center-center',
    icon: 'success',
    showConfirmButton: false,
    timer: 1500
  })
}
const delay = async (delayInms=3000)=> {
    return new Promise(resolve => {
      setTimeout(() => {
        resolve(2);
      }, delayInms);
    });
  }