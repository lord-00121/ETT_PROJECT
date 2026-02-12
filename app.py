import streamlit as st
import tempfile
import os
from PyPDF2 import PdfReader

@st.cache_resource
def load_llm():
    return None

@st.cache_resource
def load_embeddings():
    return None

def extract_text_from_pdf(pdf_file):
    pdf_reader = PdfReader(pdf_file)
    text = ""
    for page in pdf_reader.pages:
        if page.extract_text():
            text += page.extract_text()
    return text

def extract_text_from_txt(txt_file):
    return txt_file.read().decode("utf-8")

def process_document(file):
    if file.name.endswith(".pdf"):
        text = extract_text_from_pdf(file)
    elif file.name.endswith(".txt"):
        text = extract_text_from_txt(file)
    else:
        st.error("Document format not supported!")
        return None
    
    return text

def main():
    st.set_page_config(page_title="AI Document QA System", layout="wide")
    st.title("ðŸ“„ AI Question Answering System")
    st.markdown("Upload a PDF or Text document, and ask questions about its content!")

    with st.sidebar:
        st.header("1. Upload Document")
        uploaded_file = st.file_uploader("Upload a PDF or TXT file", type=["pdf", "txt"])
        
        if uploaded_file is not None:
            if st.button("Process Document"):
                with st.spinner("Extracting text..."):
                    text = process_document(uploaded_file)
                    if text:
                        st.session_state.processed_text = text
                        st.success("Document processed successfully!")

if __name__ == "__main__":
    main()
