// $(".testJs").on('click',function(){
//     console.log("toto")
// })
$(".testJs").on('click',function(event){
    event.preventDefault();
    var url = $(".testJs").attr('href')
    axios.get(url).then(function(response){
        console.log(response)
        var tab = response.data.message
        var i =0
        // while(i<tab.length){
            $(".affich").append(response.data)
            i++
        // }
    })
    
});
$(".find").on('keyup',function(event){
    event.preventDefault();
    var url = $(".find").data('set')
    var txt = $(this).val()
    console.log(url)
    axios.get(url,{
        params: {
          result: txt
        }
      }).then(function(response){
          var tab = response.data.message
          $(".affich").empty()
            if($(".find").val() == ''){
                $(".affich").empty()
            }
            else{
                $(".affich").append(response.data)
            }
           
    })
    
});
$(".js_like_link").on('click',function(e){
    e.preventDefault();
    const url = $(this).attr('href');
    console.log(url);
    const span = $(this).find('.js_like')
    const icone = $(this).find('i')
    axios.get(url).then(function(response){
           const likes = response.data.likes
           span.text(likes)
           if(icone.hasClass('fas')){
               icone.removeClass('fas')
               icone.addClass('far')
           }
           else{
                icone.removeClass('far')
                icone.addClass('fas')
           }
    }).catch(function(error){
        if(error.response.status === 403){
            alert("vous ne pouvez pas liker un questionnaire si vous n'êtes pas connecté !!!")
        }
    })
})
$(".newAjaxProp").on('click',function(e){
    e.preventDefault();
    const url = $(this).attr('href');
    console.log(url);
   
    axios.post(url).then(function(response){
           $(".ajaxProp").append(response.data)
    }).catch(function(error){
        if(error.response.status === 403){
            alert("erreur")
        }
    })
})
$(".submitAjax").on('click',function(e){
    e.preventDefault();
    const url = $(this).attr("href")
    let id_quest = $(this).attr("id")
    let text = $("."+id_quest).find(".text").val()
    let correct = $("."+id_quest).find(".correct").prop('checked')
    console.log(id_quest,correct,text)
    if(correct){
        correct = 1
    }
    else{
        correct = 0
    }
    console.log(url,id_quest,text,correct)
   
    axios.get(url,{
        params: {
          id: id_quest,
          text: text,
          correct: correct
        }
        }).then(function(response){
          $(".ajaxProp_"+id_quest).append(response.data)
              $(".empty").empty()
              $('.delProp').on('click',function(event){
                event.preventDefault();
                const url = $(this).attr("href")
                data = $(this).attr("data")
                axios.get(url,{
                                params: {
                                id: data,
                            }
                        }).then(function(response){
                            $("#"+response.data.id).slideUp()
                            // prop = $("#"+response.data.id).remove()
                            
                            
                        }).catch(function(error){
                            if(error.response.status === 403){
                                alert("erreur")
                            }
                        })
                    }) 
                }).catch(function(error){
                if(error.response.status === 403){
                    alert("erreur")
                }
        })
})
$('.delProp').on('click',function(event){
    event.preventDefault();
    const url = $(this).attr("href")
    data = $(this).attr("data")
    console.log(url)
   axios.get(url,{
        params: {
          id: data,
        }
      }).then(function(response){
        $("#"+response.data.id).slideUp()
        // prop = $("#"+response.data.id).remove()
        }).catch(function(error){
            if(error.response.status === 403){
                alert("erreur")
            }
        })
})
$(".ajoutQuest").on("click",function(){
    $(".formQuest").toggleClass("hide");
})
