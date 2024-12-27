const taskModal =document.getElementById('taskModal');
const icon = document.getElementById('icon');


taskModal.style.display="none";
icon.addEventListener('click',function(){
   taskModal.style.display="block";

});