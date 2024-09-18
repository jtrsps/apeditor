document.addEventListener('DOMContentLoaded', () => {
    const codeArea = document.getElementById('code-area');
    const lineNumbers = document.getElementById('line-numbers');
    const fileList = document.getElementById('file-list');
    const saveButton = document.getElementById('save-button');

    let currentFile = '';
    const fileContents = {};

    function updateLineNumbers() {
        const lines = codeArea.value.split('\n');
        lineNumbers.innerHTML = lines.map((_, index) => index + 1).join('<br>');
    }

    function saveCurrentFile() {
        if (currentFile) {
            fileContents[currentFile] = codeArea.value;
            console.log(`Contenido guardado para ${currentFile}`);
        }
    }

    function loadFile(fileName) {
        saveCurrentFile();
        currentFile = fileName;
        codeArea.value = fileContents[fileName] || `// Contenido del archivo ${fileName}\n\n// Escribe tu código aquí...`;
        updateLineNumbers();
    }

    codeArea.addEventListener('input', updateLineNumbers);
    codeArea.addEventListener('scroll', () => {
        lineNumbers.scrollTop = codeArea.scrollTop;
    });

    fileList.addEventListener('click', (e) => {
        if (e.target.tagName === 'LI') {
            loadFile(e.target.textContent);
        }
    });

    saveButton.addEventListener('click', () => {
        saveCurrentFile();
        saveToServer();
    });

    codeArea.addEventListener('keydown', (e) => {
        if (e.key === 'Tab') {
            e.preventDefault();
            const start = codeArea.selectionStart;
            const end = codeArea.selectionEnd;
            codeArea.value = codeArea.value.substring(0, start) + '    ' + codeArea.value.substring(end);
            codeArea.selectionStart = codeArea.selectionEnd = start + 4;
        }
    });

    function saveToServer() {
        if (!currentFile) return;

        const content = fileContents[currentFile];
        
        fetch('save.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                fileName: currentFile,
                content: content
            }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(`Archivo ${currentFile} guardado exitosamente en el servidor.`);
            } else {
                alert(`Error al guardar el archivo: ${data.message}`);
            }
        })
        .catch((error) => {
            console.error('Error:', error);
            alert('Hubo un error al intentar guardar el archivo.');
        });
    }

    // Inicializar con el primer archivo
    loadFile('index.html');
});