const parentBg = document.querySelector('.ser-images');

const childBg = document.querySelectorAll('.services_sec .link-list .ac-btn');

const textElements = document.querySelector('.text');

childBg.forEach(el => {
  el.addEventListener('mouseover', (e) =>  {
    const id = el.getAttribute('data-id');
    const bgEl = parentBg.querySelector(`.img-${id}`);
    
    parentBg.querySelectorAll("img").forEach( img => {
     img.style.display = 'none';
     img.style.animation = '';
    });
     bgEl.style.display = 'block';
     bgEl.style.animation = 'anima 3s ease forwards';
  })
})