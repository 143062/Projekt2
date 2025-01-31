<x-mail::message>
# Witaj, {{ $user['login'] }}! 🎉

Dziękujemy za rejestrację w serwisie **N**!  
Cieszymy się, że do nas dołączyłeś.

## Co dalej?
Możesz teraz zacząć tworzyć i udostępniać notatki!

<x-mail::button :url="'http://localhost:8080/login'">
Zaloguj się
</x-mail::button>



Dziękujemy,  
**Zespół N**
</x-mail::message>
