@props(['topic' => null, 'userId' => null])

<x-advanced-notifications::firebase-scripts :topic="$topic" />
<x-advanced-notifications::echo-scripts :userId="$userId" />
