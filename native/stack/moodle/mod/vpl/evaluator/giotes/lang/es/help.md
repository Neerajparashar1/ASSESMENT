## MANUAL DE GIOTES

**GIOTES** (General Input/Output Test Evaluation System) es un subplugin evaluador para **VPL** diseñado para reemplazar a **BIOTES**, el evaluador predeterminado.
Este sistema permite a los docentes evaluar automáticamente los programas de los estudiantes definiendo casos de prueba que especifican la entrada del programa y la salida esperada.

### ❓ ¿Qué es GIOTES?

GIOTES es un framework general para evaluar entregas de programación escritas en casi cualquier lenguaje.
Se ejecuta como un subplugin evaluador de VPL para Moodle ([VPL][1]) y genera informes y calificaciones para ese entorno.

Los objetivos del framework son:

* **Integración con VPL.** Plug-and-play dentro del familiar VPL para Moodle.
* **Fácil de usar.** Escribir casos de prueba en un formato simple y legible.
* **Orientado a informes.** Informes totalmente personalizables.
* **Compatibilidad con BIOTES.** Ejecuta los mismos archivos `vpl_evaluate.cases` utilizados por el framework VPL predeterminado anterior.

GIOTES mantiene el lenguaje de texto plano `declaración = valor` que los profesores ya conocen de **BIOTES** y ejecuta los mismos archivos *`vpl_evaluate.cases`*.
Las declaraciones no distinguen entre mayúsculas y minúsculas, y el espaciado es flexible.

Agrega:

* Marcas cortas personalizables para mostrar tipos de resultados de puerbas de casos: superada, fallida, tiempo agotado y error.
* Mensajes detallados personalizables para los distintos tipos de resultados.
* Un rico conjunto de etiquetas (placeholders) que puedes insertar en tus mensajes.
* La posibilidad de modificar el **formato de título de caso**.
* Permite establecer un token de **Final multilínea**, lo que permite detener un valor multilínea en cualquier token que elijas en vez detenerse cuando aparece una nueva declaración válida.
* Límites de tiempo por caso.
* La comprobación del "exit code" puede ser **requerida** (AND) o **suficiente** (OR) para superar un caso de prueba.

---

### ⚡Inicio rápido


```
# vpl_evaluate.cases (primeros pasos). Esto es un comentario

Case = Suma de dos enteros
Input =3 4
Output = 7
Output = "El resultado es 7"
```

En una actividad VPL, selecciona GIOTES como el evaluador y habilita la evaluación automática en las opciones de Ejecución. Sube este archivo como **Casos de prueba**.
Cuando el estudiante o el profesor usen la acción evaluar, GIOTES ejecutará el programa del estudiante, le proporcionará la entrada `3 4`, comparará la salida con ambas posibilidades esperadas, y calificará automáticamente.

---

## 📝 El lenguaje

El **lenguaje GIOTES** define cómo se escriben, organizan e interpretan los casos de prueba.
Es un formato de texto plano ligero diseñado para ser **legible por humanos** para profesores y **legible por máquinas** para el evaluador.
Usando reglas simples `declaración = valor`, puedes describir entradas de programa, salidas esperadas, límites de tiempo, reglas de calificación y personalización de informes.
Esta sección explica la **estructura** y **declaraciones** disponibles en archivos `vpl_evaluate.cases`, con ejemplos que muestran cómo construir definiciones de prueba correctas y flexibles.

### 📦 Estructura general de definiciones de prueba (`vpl_evaluate.cases`)

El archivo `vpl_evaluate.cases` puede contener:

* Declaraciones de **valores predeterminados globales** (opcional) — se aplican a todos los casos a menos que se sobrescriban.
* **Bloques de casos** — cada uno comienza con `case =` y describe un caso de prueba.  
    Todas las configuraciones dentro de un caso sobrescriben los valores predeterminados globales, excepto para `output =`, que **agrega** posibilidades de resultados válidos adicionales.

*Resumen del formato*

```text
  ├─── 📦 Declaraciones generales y Valores predeterminados  (ámbito global, opcional)
  │    • Establecido antes del primer bloque 'case ='.
  │    • Define valores predeterminados para todos los casos.
  │    • Ejemplos comunes:
  │        ├─ Grade reduction = 1
  │        ├─ Time limit = 3
  │        ├─ Fail mark = 🔴
  │        ├─ Pass mark = 🟢
  │        └─ Case title format = 🧪 <<<case_title>>> — <<<test_result_mark>>>
  │
  ├─── # Secuencia de casos  (uno o más bloques "case = ...")
  ├─── 📝 Caso ejemplo 1:
  │     ├─ case = caso de prueba 1
  │     ├─ input = 6 3
  │     └─ output = 2
  │
  ├─── 📝 Caso ejemplo 2:
  │     ├─ case = caso de prueba 2
  │     ├─ input = 16 4
  │     └─ output = 4
  │
  ├─── 📝 Caso ejemplo 3:
  │     ├─ case = caso de prueba 3
  │     ├─ input = 1 0
  │     └─ output = División por cero
  │
  └─── 📝 Caso ejemplo N
        ├─ case = caso de prueba N
        ├─ input = -4 2
        └─ output = Número negativo
```

* Cada bloque `case =` puede sobrescribir localmente los valores predeterminados globales.
* Cada `output =` agrega un **nuevo** resultado valido (no **reemplaza** los anteriores).
* Los casos se evalúan secuencialmente, en el orden escrito.
* Si una declaración se repite, el **último** valor gana (excepto para `output =`).

---

#### ⚙️ Declaraciones básicas

* **Case =** una línea con la descripción del caso (**requerido**)

  Ejemplo:
  >`Case = Primer caso de prueba para suma de n números`

* **Input =** texto enviado a `stdin` (puede abarcar múltiples líneas)

  Ejemplo:

  >```
  Input =3
  1
  2
  5
  ```

* **Output =** el resultado esperado. Puedes establecer múltiples líneas `output =` para aceptar respuestas válidas alternativas.

  Ejemplo:

  >```
  Output = 8
  Output = La suma es ocho
  ```

Hay diferentes tipos de salida; el tipo se **infiere del formato del valor**:

*Si el valor de `output` es …*

* **Solo números** → Entonces se aplica la verificación de "**numbers**". Para usar este tipo de verificación, asegúrate de escribir solo números, sin nada más. Los números pueden ser enteros, en coma flotantes en notación decimal o científica.
  Al verificar, los caracteres no numéricos en la salida del programa se ignoran. Para números de punto flotante, la igualdad se determina usando tolerancia relativa: `abs((esperado - actual) / esperado) < 0.0001` si `esperado == 0` entonces se usa `abs(actual) < 0.0001`. Tenga en cuenta que la tolerancia en este momento es un valor fijo. Para enteros, se requiere igualdad exacta. Para enteros definidos en la declaración "output=" se espera entero en la salida del programa. Para como flotantes definidos en la declaración "output=" se espera como flotante o entero en la salida del programa.

  Ejemplo:
  >`Output = 2 3.00001`

  ✅ *Salidas del programa que **coinciden**:*

  * `El resultado es 2 y 3`
  * `El resultado es:`  
      `2`  
      `3`
  * `2 3.00001`
  * `2 - 3`
  * `2 3`

  ❌ *Salidas del programa que **no coinciden**:*

  * `El resultado es 1, 2 y 3`
  * `2.0 3`
  * `2.3`
  * `El resultado es 2, 3 y 4`  
      `2 3`

* **Texto entre comillas dobles** → Entonces se aplica la verificación de "**exact text**".
  Si el texto esperado no termina con nueva línea, se tolera una nueva línea al final en la salida del programa, pero no se aceptan espacios al final.

  Ejemplo:  
  > `Output = "Todo·bien"`

  ✅ *Salidas del programa que **coinciden**:*

  * `Todo·bien`  
  * `Todo·bien↵`

  ❌ *Salidas del programa que **no coinciden**:*
  
  * `todo·bien`
  * `todo·bien·`
  * `Todo··bien↵`
  * `Todo·bien·↵`

  Nota que en estos ejemplos "·" significa un espacio y "↵" una nueva línea.

* **Texto plano** → Si el valor establecido con "output=" no coincide con ningún otro tipo de verificación, entonces se aplica la verificación palabra por palabra de "**text**", GIOTES ignora puntuación, mayúsculas/minúsculas y saltos de línea, y comprueba que el texto introducido coincide con la última secuencia de palabras en la salida del programa. Este tipo de verificación pretende ser flexible con la salida generada por el programa del estudiante mientras sigue siendo testeable.

  Ejemplo:
  >`Output = Todo bien con 10 puntos`

  ✅ *Salidas del programa que **coinciden**:*

  * `Todo bien con 10 puntos.`
  * `Mi respuesta es: Todo bien con 10 puntos.`
  * `Todo bien con (10) puntos.`
  * `todo bien, con 10 PUNTOS!!!`
  * `  TODO "bien" con ===>>>`  
      `  -10- puntos`

  ❌ *Salidas del programa que **no coinciden**:*

  * `Todo bien con 11 puntos`
  * `Todo bien con 10 punto`
  * `Todo bien con puntos: 10`
  * `Todo bien con 10 puntos, qué más`

* **`/regex/[flags]`** → Si la salida coincide con este formato entonces se aplica la verificación de "**regular expression**" extendida POSIX-C (nota: la sintaxis POSIX difiere de PCRE).

  Banderas:

  * `i` → insensible a mayúsculas/minúsculas
  * `m` → multilínea (una **línea** correcta es suficiente para que la salida se considere válida)

  Use escapes `\n`, `\r`, `\t`, y `\\` para introducir un caracter de nueva línea, retorno de carro, tabulador y barra invertida.
  Use `^` y `$` para comprobar el contenido completo (o línea completa con bandera `m`).

  Ejemplo:
  >`Output = /^(regex|no +regex|1{3,20})\n?$/i`

  ✅ *Salidas del programa que **coinciden**:*

  * `regeX`
  * `no     regex`
  * `1111↵`
  * `11111111111111111`

  ❌ *Salidas del programa que **no coinciden**:*

  * `egex`
  * `noregex`
  * `11`
  * `cualquier cosa`  
      `no regex`
      `regex`

* **Comodín `*`** para tipos de verificación de **numbers** y **exact text** puedes usar un comodín inicial — Si el valor establecido con "output=" comienza con `*`, el caso se supera cuando el **final** de la salida del programa coincide. Note que las comprobaciones de tipo **text** ya compruban la salida como si ya tuviesen un comodín inicial; para **regular expression**, puede usar ".*" como comodín dentro de la expresión regular.

  Ejemplo:
  >`Output = * 2 3.00001`

  ✅ *Salidas del programa que **coinciden**:*

  * `El resultado es 2 y 3`
  * `El resultado es:`  
      `1`  
      `2`  
      `3`
  * `0 1 2 2 2 3.00001`

  ❌ *Salidas del programa que **no coinciden**:*

  * `El resultado es 2, 3 y 4`
  * `El resultado es 2, 3`  
      `2 3`  
      `3`

---

#### ➕ Declaraciones para añadir condiciones y penalizaciones

* **Grade reduction =** *valor* | *porcentaje%* — Cambia la penalización predeterminada `rango_calificación / número_de_casos`. Si la reducción de calificación es mayor o igual al doble de `rango_calificación` y el caso falla, las pruebas se detienen. Esto permite definir casos para detener el proceso de evaluación.

  Ejemplos:
  >`Grade reduction = 1.5`  
  >`Grade reduction = 5%`  
  >`Grade reduction = 300%`

* **Time limit =** *segundos* — Establece el límite de tiempo de ejecución por caso, globalmente o en el caso que se use. El valor por defecto es `límite_tiempo_global / número_de_casos`.

  Ejemplo:
  >`Time limit = 2.5`

* **Expected exit code =** *entero* — Establece el código de salida o exitcode esperado del programa evaluado. Por defecto, el exitcode se ignora.

  * Si es **positivo**: el caso **se supera si el código de salida coincide O la salida coincide**.
  * Si es **negativo** (se usa el valor absoluto): el caso **se supera solo si el código de salida coincide Y la salida coincide**.
  * Si es **0**: mantiene el modo OR/AND previamente seleccionado en el caso con otra declaración.

  Ejemplos:
  >`Expected exit code = 3`  
  >`Expected exit code = -5`  
  >`Expected exit code = 0`

  **Cómo se determina el resultado de una prueba que combina pruebas de salida y de exitcode:**

    | Condición                               | Salida (coincide) | Salida (no coincide)|
    |-----------------------------------------|:-----------------:|:-------------------:|
    | **Código de salida no establecido**    | ✅                | ❌                 |
    | **Código de salida positivo (coincide)**| ✅                | ✅                 |
    | **Código de salida positivo (no coincide)**| ✅             | ❌                 |
    | **Código de salida negativo (coincide)**| ✅                | ❌                 |
    | **Código de salida negativo (no coincide)**| ❌             | ❌                 |

Nota: Los códigos de salida del programa en sí no pueden ser negativos; un valor negativo aquí solo se usa para indicar el comportamiento "AND".

---

#### 🧩 Otras declaraciones de control

* **Program to run =** *ruta* — Reemplaza el ejecutable a probar por el programa en *ruta*.

  Ejemplo:
  >`Program to run = /usr/bin/cat`

* **Program args =** *arg1 arg2 …* — Argumentos pasados al programa a probar (o a **Program to run** si está establecido).

  Ejemplo:
  >`Program args = output.txt`

* **Variation =** *id\_variación* — El caso se considera solo si la variable de entorno `VPL_VARIATION` es igual a *id\_variación* (insensible a mayúsculas/minúsculas).
  De lo contrario, se trata como si no existiera.

  Ejemplo:
  >`Variation = variacion_uno`

---

#### 🖋️ Declaraciones para personalizar el informe

Estas declaraciones se establecen comúnmente **globalmente** al inicio del archivo para estandarizar el informe.
También se pueden establecer **por caso** para personalizar casos individuales.

* **Fail message =** o **Fail output message =** — Texto personalizado mostrado cuando el caso falla (puede abarcar varias líneas).

  Ejemplo:

  >```
  Fail output message=Ejecutando tu código con esta entrada:
  <<<input>>>
  Esperamos: <<<expected_output_inline>>>
  Pero obtenemos: <<<program_output_inline>>>
  ```

* **Pass message =** — Texto personalizado mostrado cuando el caso se supera (por defecto no se muestra nada).

  Ejemplo:

  >```
  Pass message=¡Excelente! Ejecutando tu código con esta entrada:
  <<<input>>>
  Obtenemos la respuesta correcta: <<<program_output_inline>>>
  ```

* **Timeout message =** — Texto personalizado mostrado cuando el caso de prueba supera su tiempo límite.

  Ejemplo:

  >```
  Timeout message=Tu código puede contener un bucle infinito.
  Verifica que las condiciones del bucle cambian y que no tengas enlaces circulares en una lista enlazada.
  ```

* **Fail exit code message =** — Texto personalizado mostrado cuando el código de salida no coincide y el caso de prueba falla.

  Ejemplo:

  >```
  Fail exit code message=Para esta entrada el código de salida de tu programa fue incorrecto:
  <<<input>>>
  Esperábamos: <<<expected_exit_code>>>
  Pero obtuvimos: <<<exit_code>>>
  ```

* **Case title format =** — Formato de título personalizado usado al informar del resultado de un caso.
  Su valor predeterminado es: `Test <<<case_id>>>: <<<case_title>>>`

  Ejemplo:
  >`Case title format = Prueba <<<case_id>>>/<<<num_tests>>>: <<<case_title>>> <<<test_result_mark>>>`

* **Multiline end =** *TOKEN* — La **siguiente** declaración de valor multilínea se expande hasta una línea que sea exactamente igual a *TOKEN*. 
  Esto te permite incluir líneas que de otro modo serían analizadas como nuevas declaraciones. Este comportamiento se aplica solo para la siguiente declaración.

  Ejemplo:

  >```
  Multiline end = FIN_DEL_TEXTO
  Input = esta es una entrada
  que contiene cualquier cosa
  output= esta línea es parte de la entrada
  la siguiente línea termina la entrada
  FIN_DEL_TEXTO
  ```

---

#### 🌍 Declaraciones con efecto global

* **Fail mark / Pass mark / Timeout mark / Error mark** —
  Establece texto a mostrar normalmente a través del marcador de posición (placeholder)`<<<test_result_mark>>>`.
  La marca se expande a alguno de los valores establecidos según el resultado de la prueba: *falla*, *superada*, *tiempo agotado*, o *error*.

  Ejemplo:

  >```
  Fail mark = [❌ resultado incorrecto]
  Pass mark = [✅ prueba superada]
  Error mark = [🛑 error inesperado]
  Timeout mark = [⏰ tiempo agotado]
  ```

* **Final report message =** — Mensaje añadido al final del informe de pruebas.

  Ejemplo:

  >```
  Final report message = - Resumen
  ✅ Pruebas superadas <<<num_tests_passed>>>
  ❌ Pruebas falladas <<<num_tests_failed>>>
  ```

* Cuando la misma declaración aparece más de una vez en la configuración global o dentro de una definición de caso, la **última** gana.

---

#### 🔖 Marcadores de posición

Los marcadores de posición (placeholders) tienen el formato `<<<nombre_marcador_posición>>>` 🔖 y pueden ser usados en título de casos (**T**), mensajes de caso de prueba personalizados (**M**) e informe final (**F**). La siguiente tabla muestra todos los marcadores de posición, dónde están disponibles ✅ y puedes usarlos, y una descripción 📄 de lo que expanden.

| 📝Marcador de posición        | ✅Dispon | 📄Descripción                          |
| ------------------------------ |:-------:| ---------------------------------------------- |
| `<<<case_id>>>`                | T M| El índice basado en 1 del caso de prueba.|
| `<<<case_title>>>`             | T M| El título del caso establecido con `case =`.|
| `<<<test_result_mark>>>`       | T M| Se expande a una de las marcas establecidas por `Fail mark =`, `Pass mark =`, `Timeout mark =`, o `Error mark =`, dependiendo del resultado del caso de prueba. |
| `<<<fail_mark>>>`              | T M| El texto establecido por `Fail mark =`. |
| `<<<pass_mark>>>`              | T M| El texto establecido por `Pass mark =`. |
| `<<<timeout_mark>>>`           | T M| El texto establecido por `Timeout mark =`. |
| `<<<error_mark>>>`             | T M| El texto establecido por `Error mark =`. |
| `<<<input>>>`                  | M| El texto establecido por `Input =` (multilínea, preformateado). |
| `<<<input_inline>>>`           | M| El texto `Input =` en formato línea; códigos de control y espacios son reemplazados (ej., nueva línea `↵`, espacio `␣`). |
| `<<<expected_output>>>`        | M| El texto establecido en el **primer** `Output =` del caso (multilínea, preformateado). |
| `<<<expected_output_inline>>>` | M| El texto del primer `Output =` en formato línea; códigos de control y espacios son reemplazados (ej., nueva línea `↵`, espacio `␣`). |
| `<<<check_type>>>`             | M| El tipo de verificación para el primer `Output =` (uno de: `numbers`, `text`, `exact text`, `regular expression`). |
| `<<<program_output>>>`         | M| La salida del programa (multilínea, preformateada). |
| `<<<program_output_inline>>>`  | M| El texto de salida del programa en formato línea; códigos de control y espacios son reemplazados (ej., nueva línea `↵`, espacio `␣`) |
| `<<<expected_exit_code>>>`     | M| El código de salida esperado establecido por `Expected exit code =`. |
| `<<<exit_code>>>`              | M| El código de salida real de la ejecución del programa. |
| `<<<time_limit>>>`             | M| El límite de tiempo aplicado al caso de prueba actual. |
| `<<<grade_reduction>>>`        | M| La penalización aplicada si el caso no se supera. |
| `<<<num_tests>>>`              | T M F| Número total de casos de prueba (después de filtrar por variación). |
| `<<<num_tests_run>>>`          | F| Número de casos de prueba realmente ejecutados (puede ser menor que `<<<num_tests>>>` si se detiene por tiempo agotado global o una parada explícita). |
| `<<<num_tests_passed>>>`       | F| Número de casos superados. |
| `<<<num_tests_failed>>>`       | F| Número de casos que fallaron debido a discrepancia de salida o código de salida incorrecto. |
| `<<<num_tests_timeout>>>`      | F| Número de casos que agotaron su tiempo. |
| `<<<num_tests_error>>>`        | F| Número de casos que terminaron con errores inesperados. |

✅Dispon leyenda: T = Formato de título de caso, M = Mensajes personalizados, F = Informe final

#### 🧮 Cómo se calcula la calificación

1. `rango_calificación = VPL_GRADEMAX − VPL_GRADEMIN` (por defecto, si no se establece, son 10 − 0 = 10).
2. Para cada caso **no superado**, GIOTES resta una penalización de la calificación.
   Por defecto la penalización es `rango_calificación / número_de_casos`.
3. La declaración **Grade reduction=** reemplaza la penalización predeterminada (puede ser absoluta o un porcentaje de `rango_calificación`).
4. La calificación final se limita al rango de calificación de la actividad.

**Fórmula**

```
calificación_mínima = VPL_GRADEMIN           (predeterminado 0)
calificación_máxima = VPL_GRADEMAX           (predeterminado 10)
rango_calificación   = calificación_máxima - calificación_mínima

penalizaciones_totales = Σ(reducción_calificación de cada caso no aprobado)

calificación_final = calificación_mínima + (rango_calificación - penalizaciones_totales)
```

---

#### 🌐 Variables de entorno reconocidas

* `VPL_GRADEMIN` (predeterminado `0`)
* `VPL_GRADEMAX` (predeterminado `10`)
* `VPL_MAXTIME` — segundos totales para **todos** los casos (predeterminado `20`)
* `VPL_VARIATION` — id de variación actual (vacío por defecto)

#### 📂 Ejemplo `vpl_evaluate.cases`

```
# Valores predeterminados globales
Case title format = Prueba <<<case_id>>>: <<<case_title>>> <<<test_result_mark>>>
Fail output message = Para la entrada "<<<input_inline>>>":
Se esperaba <<<expected_output_inline>>>, se obtuvo <<<program_output_inline>>>
Timeout message = Tu programa tardó demasiado.
Final report message =
-Resumen:
✅ Pruebas superadas: <<<num_tests_passed>>>
❌ Pruebas fallidas: <<<num_tests_failed>>>
⏰ Pruebas tiempo agotado: <<<num_tests_timeout>>>
🛑 Pruebas con errores: <<<num_tests_error>>>

Fail mark = ❌
Pass mark = ✅
Timeout mark = ⏰
Error mark = 🛑
Grade reduction = 1
Time limit = 2

# --- Casos de prueba ---

Case = Suma de dos enteros
Input = + 3 4
Output = 7
Output = "El resultado es 7"

Case = División
Input = / 10 2
Output = 5
Pass message = ¡División correcta!

Case = División por cero
Input = / 1 0
Output = División por cero
Expected exit code = -1
# debe coincidir salida Y código de salida

Case = Ejecución lenta
Input = bucle
Output = Hecho
Time limit = 0.5

```

## 📜 Licencia y autoría

© Copyright 2025, Juan Carlos Rodríguez-del-Pino [jc.rodriguezdelpino@ulpgc.es](mailto:jc.rodriguezdelpino@ulpgc.es).

Esta documentación está licenciada bajo una 
[Licencia Creative Commons Atribución-NoComercial-SinDerivadas 4.0 Internacional](https://creativecommons.org/licenses/by-nc-nd/4.0/).

[![Licencia CC BY-NC-ND 4.0](https://licensebuttons.net/l/by-nc-nd/4.0/88x31.png)](https://creativecommons.org/licenses/by-nc-nd/4.0/)

---

*¡Disfruta la calificación automatizada con GIOTES!*

[1]: https://vpl.dis.ulpgc.es "Documentación del Laboratorio Virtual de Programación para Moodle (VPL)"