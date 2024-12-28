document.addEventListener('DOMContentLoaded', () => {
   const detailsModal = document.getElementById('detailsModal');

   function showModal() {
      detailsModal.classList.add('active');
   }

   // function hideModal() {
   //    detailsModal.classList.remove('active');
   // }

   // const cancelBtn = document.getElementById('cancelBtn');

   const icone = document.getElementById('icon');
   console.log(icone);

   icone.addEventListener('click', showModal);
});