<?php
return ['token'=>env('GITHUB_TOKEN'),'webhook_secret'=>env('GITHUB_WEBHOOK_SECRET'),'timeout'=>(int)env('GITHUB_TIMEOUT',15)];
