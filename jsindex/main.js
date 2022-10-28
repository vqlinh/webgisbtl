var btnOpen= document.querySelector('.search-box-btn')


btnOpen.addEventListener('click',function(){
    this.parentElement.classList.toggle('open')
    this.previousElementSibling.focus()
})
